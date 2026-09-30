<?php

namespace Database\Seeders;

use App\Models\Compra;
use App\Models\Denominacion;
use App\Models\Distrito;
use App\Models\Lote;
use App\Models\Nula;
use App\Models\Realizacion;
use App\Models\TipoEspecie;
use App\Models\Traslado;
use App\Models\TrasladoDetalle;
use App\Models\Ubicacion;
use App\Models\UbicacionRango;
use App\Models\User;
use App\Services\Inventario;
use App\Services\Tarifas;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Datos iniciales de especies municipales, de noviembre 2025 a marzo 2026, tomados de los libros de Tesoreria
 * (database/data/especies_nov2025_mar2026.csv). Todo entra por la bodega: las existencias de cada distrito al
 * 31/10/2025 y cada compra al M.H. se registran como compra y se trasladan el mismo dia al distrito. Despues van
 * el traslado entre distritos, las cajas de Metapan, las realizaciones y las nulas. Solo corre con las tablas vacias.
 */
class DatosInicialesEspeciesSeeder extends Seeder
{
    public const ARCHIVO = 'data/especies_nov2025_mar2026.csv';

    private int $usuario;
    private array $distritos;
    private array $tipos;
    private array $denominaciones = [];
    private array $precios = [];

    public function run(): void
    {
        if (Compra::exists() || Traslado::exists() || Realizacion::exists() || Nula::exists()) {
            $this->command?->warn('Ya hay movimientos de especies registrados: no se cargan los datos iniciales.');
            return;
        }

        $this->usuario   = User::where('rol', 'admin')->orderBy('id')->value('id')
                           ?? throw new RuntimeException('Falta un usuario administrador para registrar los datos iniciales.');
        $this->distritos = Distrito::pluck('id', 'codigo')->all();
        $this->tipos     = TipoEspecie::pluck('id', 'nombre')->all();

        $filas = collect($this->leer())->groupBy('movimiento');

        DB::transaction(function () use ($filas) {
            $this->comprar('SALDO-INICIAL', $filas->get('SALDO_INICIAL', collect()),
                'Existencias de los distritos al 31/10/2025 según sus libros de especies (carga inicial)');
            foreach ($filas->get('COMPRA', collect())->groupBy('factura') as $factura => $compra) {
                $this->comprar($factura, $compra, 'Compra al M.H. registrada en los libros de especies (carga inicial)');
            }

            foreach ($filas->get('TRASLADO', collect()) as $f) {
                $origen   = $this->distritos[$f['distrito']];
                $traslado = Traslado::create([
                    'tipo' => 'distrito_distrito', 'origen_distrito_id' => $origen, 'distrito_id' => $this->distritos[$f['destino']],
                    'fecha' => $f['fecha'], 'observaciones' => $f['nota'] ?: null, 'usuario_id' => $this->usuario,
                ]);
                foreach ($this->partes($f, $origen) as [$detalle, $inicio, $fin]) {
                    TrasladoDetalle::create(['traslado_id' => $traslado->id, 'lote_id' => $detalle->lote_id,
                        'numero_inicio' => $inicio, 'numero_fin' => $fin, 'cantidad' => $fin - $inicio + 1]);
                }
            }

            // Cajas de Metapan: las existencias iniciales ya traen su caja; despues, las reasignaciones del libro
            $cajas = $filas->get('SALDO_INICIAL', collect())->where('ubicacion', '!=', '')->merge($filas->get('ASIGNACION_CAJA', collect()));
            foreach ($cajas as $f) {
                UbicacionRango::create([
                    'ubicacion_id'    => Ubicacion::where('distrito_id', $this->distritos[$f['distrito']])->where('nombre', $f['ubicacion'])->firstOrFail()->id,
                    'tipo_especie_id' => $this->denominacion($f)->tipo_especie_id, 'serie' => $f['serie'],
                    'numero_inicio'   => $f['del'], 'numero_fin' => $f['al'], 'fecha' => $f['fecha'], 'usuario_id' => $this->usuario,
                ]);
            }

            foreach ($filas->get('REALIZACION', collect()) as $f) {
                $distrito = $this->distritos[$f['distrito']];
                $den      = $this->denominacion($f);
                Realizacion::create([
                    'tipo_especie_id' => $den->tipo_especie_id, 'denominacion_id' => $den->id, 'serie' => $f['serie'],
                    'distrito_id'     => $distrito, 'numero_inicio' => $f['del'], 'numero_fin' => $f['al'], 'cantidad' => $f['cantidad'],
                    'fecha'           => $f['fecha'], 'monto_cobrado' => round($f['cantidad'] * ($this->precio($distrito, $den) ?? 0), 2),
                    'usuario_id'      => $this->usuario,
                ]);
            }

            foreach ($filas->get('NULA', collect()) as $f) {
                $distrito = $this->distritos[$f['distrito']];
                foreach ($this->partes($f, $distrito) as [$detalle, $inicio, $fin]) {
                    Nula::create(['traslado_detalle_id' => $detalle->id, 'distrito_id' => $distrito, 'numero_inicio' => $inicio,
                        'numero_fin' => $fin, 'fecha' => $f['fecha'], 'usuario_id' => $this->usuario]);
                }
            }

            $this->verificar();
        });

        $this->command?->info('Datos iniciales cargados: ' . $filas->map->count()->map(fn($n, $k) => "$k $n")->implode(', ') . '.');
    }

    private function leer(): array
    {
        $fh = fopen(database_path(self::ARCHIVO), 'r') ?: throw new RuntimeException('No se encontró ' . self::ARCHIVO);
        $cabecera = fgetcsv($fh, null, ',', '"', '');
        $filas    = [];
        while (($r = fgetcsv($fh, null, ',', '"', '')) !== false) {
            if ($r === [null]) continue;
            $f = array_combine($cabecera, $r);
            $f['serie'] = Inventario::serie($f['serie']);
            foreach (['del', 'al', 'cantidad'] as $k) $f[$k] = (int) $f[$k];
            if ($f['al'] - $f['del'] + 1 !== $f['cantidad']) {
                throw new RuntimeException("Fila con cantidad que no cuadra con el rango: " . implode(',', $r));
            }
            $filas[] = $f;
        }
        fclose($fh);
        return $filas;
    }

    // Sin valor (tipos que se llevan en unidades) se usa la unica denominacion del tipo
    private function denominacion(array $f): Denominacion
    {
        return $this->denominaciones[$f['tipo_especie'] . '|' . $f['valor']] ??= (function () use ($f) {
            $tipoId = $this->tipos[$f['tipo_especie']] ?? throw new RuntimeException("Tipo de especie desconocido: {$f['tipo_especie']}");
            $dens   = Denominacion::where('tipo_especie_id', $tipoId)
                ->when($f['valor'] !== '', fn($q) => $q->where('valor', $f['valor']))->get();
            if ($dens->count() !== 1) {
                throw new RuntimeException("No hay una única denominación de {$f['tipo_especie']} para el valor '{$f['valor']}'.");
            }
            return $dens->first();
        })();
    }

    private function precio(int $distritoId, Denominacion $den): ?float
    {
        $clave = "$distritoId|{$den->id}";
        if (!array_key_exists($clave, $this->precios)) {
            $this->precios[$clave] = Tarifas::precioVenta($distritoId, $den);
        }
        return $this->precios[$clave];
    }

    /** Registra una compra con un lote por fila y la traslada a cada distrito el mismo dia en que la recibe. */
    private function comprar(string $factura, Collection $filas, string $observaciones): void
    {
        if ($filas->isEmpty()) return;

        $compra = Compra::create(['numero_factura' => $factura, 'fecha' => $filas->min('fecha'), 'observaciones' => $observaciones,
                                  'monto_total' => 0, 'user_id' => $this->usuario]);
        $monto = 0;
        foreach ($filas->groupBy(fn($f) => $f['fecha'] . '|' . $f['distrito']) as $entrega) {
            $traslado = Traslado::create([
                'tipo' => 'bodega_distrito', 'distrito_id' => $this->distritos[$entrega[0]['distrito']], 'fecha' => $entrega[0]['fecha'],
                'observaciones' => "Entrega de la factura N° {$factura}", 'usuario_id' => $this->usuario,
            ]);
            foreach ($entrega as $f) {
                $den  = $this->denominacion($f);
                $lote = Lote::create(['compra_id' => $compra->id, 'tipo_especie_id' => $den->tipo_especie_id, 'denominacion_id' => $den->id,
                                      'serie' => $f['serie'], 'cantidad_total' => $f['cantidad']]);
                $lote->rangos()->create(['numero_inicio' => $f['del'], 'numero_fin' => $f['al']]);
                TrasladoDetalle::create(['traslado_id' => $traslado->id, 'lote_id' => $lote->id,
                                         'numero_inicio' => $f['del'], 'numero_fin' => $f['al'], 'cantidad' => $f['cantidad']]);
                $monto += $f['cantidad'] * (float) $den->valor;
            }
        }
        $compra->update(['monto_total' => $monto]);
    }

    /**
     * Parte el rango de la fila segun el ingreso al distrito que lo trajo (el mas reciente hasta esa fecha):
     * [[traslado_detalle, inicio, fin], ...]. Falla si algun numero no llego al distrito o es de otra denominacion.
     */
    private function partes(array $f, int $distritoId): array
    {
        $den      = $this->denominacion($f);
        $entradas = TrasladoDetalle::with('lote')
            ->join('traslados', 'traslados.id', '=', 'traslado_detalles.traslado_id')
            ->where('traslados.distrito_id', $distritoId)->where('traslados.fecha', '<=', $f['fecha'])
            ->whereHas('lote', fn($q) => $q->where('tipo_especie_id', $den->tipo_especie_id)
                ->where(fn($q2) => $f['serie'] === null ? $q2->whereNull('serie') : $q2->where('serie', $f['serie'])))
            ->where('traslado_detalles.numero_inicio', '<=', $f['al'])->where('traslado_detalles.numero_fin', '>=', $f['del'])
            ->orderByDesc('traslados.fecha')->orderByDesc('traslado_detalles.id')
            ->get(['traslado_detalles.*']);

        $partes = [];
        $falta  = [[$f['del'], $f['al']]];
        foreach ($entradas as $d) {
            $toca = Inventario::interseccion($falta, [[(int) $d->numero_inicio, (int) $d->numero_fin]]);
            if (!$toca) continue;
            if ($d->lote->denominacion_id != $den->id) {
                throw new RuntimeException("{$f['movimiento']} {$f['del']}-{$f['al']} ({$f['tipo_especie']} {$f['valor']}): el lote que lo trajo es de otra denominación.");
            }
            foreach ($toca as [$i, $fin]) $partes[] = [$d, $i, $fin];
            $falta = Inventario::neto($falta, $toca);
        }
        if ($falta) {
            throw new RuntimeException("{$f['movimiento']} de {$f['distrito']} {$f['tipo_especie']} {$f['fecha']}: no llegaron al distrito "
                . Inventario::texto($falta) . ($f['serie'] ? " serie {$f['serie']}" : ''));
        }
        usort($partes, fn($a, $b) => $a[1] <=> $b[1]);
        return $partes;
    }

    // La historia de cada lote en la bodega y en cada distrito que lo tuvo debe ser valida
    private function verificar(): void
    {
        $pares = Lote::pluck('id')->map(fn($id) => [null, $id])->all();
        foreach (TrasladoDetalle::with('traslado')->get() as $d) {
            if ($d->traslado->distrito_id)        $pares[] = [$d->traslado->distrito_id, $d->lote_id];
            if ($d->traslado->origen_distrito_id) $pares[] = [$d->traslado->origen_distrito_id, $d->lote_id];
        }
        foreach (array_unique(array_map(fn($p) => ($p[0] ?? '') . '|' . $p[1], $pares)) as $clave) {
            [$distrito, $lote] = explode('|', $clave);
            if ($error = Inventario::verificar($distrito === '' ? null : (int) $distrito, (int) $lote)) {
                throw new RuntimeException($error);
            }
        }
    }
}
