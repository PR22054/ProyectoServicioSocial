<?php

namespace App\Services;

use App\Models\Distrito;
use App\Models\Lote;
use App\Models\Nula;
use App\Models\Realizacion;
use App\Models\TipoEspecie;
use App\Models\TrasladoDetalle;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

//existencias de especies calculadas con aritmetica de intervalos.
//un documento se identifica por (tipo, serie, numero): la misma numeracion se
//repite entre series, asi que toda comparacion de rangos va acotada por serie.
class Inventario
{
    //serie normalizada: en mayusculas y null si viene vacia
    public static function serie(?string $serie): ?string
    {
        $serie = strtoupper(trim((string) $serie));
        return $serie === '' ? null : $serie;
    }

    //serializa los movimientos de esos tipos; va al inicio de DB::transaction
    public static function bloquear(int ...$tipoIds): void
    {
        TipoEspecie::whereKey(array_unique($tipoIds))->orderBy('id')->lockForUpdate()->pluck('id');
    }

    //posiciones cuyo conteo es >= $minimo, sumando 1 por cada rango de $mas y restando 1
    //por cada rango de $menos. devuelve intervalos disjuntos, ordenados y fusionados.
    public static function neto(array $mas, array $menos = [], int $minimo = 1): array
    {
        $delta = [];
        foreach ($mas as [$i, $f]) {
            $delta[$i]     = ($delta[$i] ?? 0) + 1;
            $delta[$f + 1] = ($delta[$f + 1] ?? 0) - 1;
        }
        foreach ($menos as [$i, $f]) {
            $delta[$i]     = ($delta[$i] ?? 0) - 1;
            $delta[$f + 1] = ($delta[$f + 1] ?? 0) + 1;
        }
        ksort($delta);

        $res    = [];
        $conteo = 0;
        $desde  = null;
        foreach ($delta as $pos => $d) {
            $antes   = $conteo;
            $conteo += $d;
            if ($antes < $minimo && $conteo >= $minimo) {
                $desde = $pos;
            } elseif ($antes >= $minimo && $conteo < $minimo) {
                $res[] = [$desde, $pos - 1];
            }
        }
        return $res;
    }

    //numeros que estan en los dos grupos de intervalos
    public static function interseccion(array $a, array $b): array
    {
        return self::neto(array_merge(self::neto($a), self::neto($b)), [], 2);
    }

    //indica si el rango completo esta dentro de los intervalos
    public static function contiene(array $intervalos, int $inicio, int $fin): bool
    {
        return self::neto([[$inicio, $fin]], $intervalos) === [];
    }

    //cantidad de documentos de los intervalos
    public static function total(array $intervalos): int
    {
        return array_sum(array_map(fn($r) => $r[1] - $r[0] + 1, $intervalos));
    }

    //intervalos como texto para los mensajes (ej. 1-50, 60)
    public static function texto(array $intervalos): string
    {
        return implode(', ', array_map(fn($r) => $r[0] === $r[1]
            ? number_format($r[0])
            : number_format($r[0]) . '–' . number_format($r[1]), $intervalos));
    }

    //convierte filas con numero_inicio y numero_fin en intervalos
    private static function rangos($filas): array
    {
        return collect($filas)->map(fn($r) => [(int) $r->numero_inicio, (int) $r->numero_fin])->values()->all();
    }

    //existencia de un distrito por lote a una fecha: lo recibido menos salidas, nulas y realizaciones.
    //incluye todo lote que el distrito haya recibido, aunque hoy tenga cero.
    public static function distrito(int $distritoId, ?int $tipoId = null, ?Carbon $hasta = null, ?int $sinRealizacion = null): Collection
    {
        $fecha = $hasta?->toDateString();

        $movimientos = fn(string $columna) => TrasladoDetalle::query()->toBase()
            ->join('traslados', 'traslados.id', '=', 'traslado_detalles.traslado_id')
            ->join('lotes', 'lotes.id', '=', 'traslado_detalles.lote_id')
            ->where("traslados.$columna", $distritoId)
            ->when($tipoId, fn($q) => $q->where('lotes.tipo_especie_id', $tipoId))
            ->when($fecha, fn($q) => $q->where('traslados.fecha', '<=', $fecha))
            ->get(['traslado_detalles.lote_id', 'traslado_detalles.numero_inicio', 'traslado_detalles.numero_fin'])
            ->groupBy('lote_id');

        $entradas = $movimientos('distrito_id');
        $salidas  = $movimientos('origen_distrito_id');

        $nulas = Nula::query()->toBase()
            ->join('traslado_detalles', 'traslado_detalles.id', '=', 'nulas.traslado_detalle_id')
            ->join('lotes', 'lotes.id', '=', 'traslado_detalles.lote_id')
            ->where('nulas.distrito_id', $distritoId)
            ->when($tipoId, fn($q) => $q->where('lotes.tipo_especie_id', $tipoId))
            ->when($fecha, fn($q) => $q->where('nulas.fecha', '<=', $fecha))
            ->get(['traslado_detalles.lote_id', 'nulas.numero_inicio', 'nulas.numero_fin'])
            ->groupBy('lote_id');

        $reales = Realizacion::query()->toBase()
            ->where('distrito_id', $distritoId)
            ->when($tipoId, fn($q) => $q->where('tipo_especie_id', $tipoId))
            ->when($fecha, fn($q) => $q->where('fecha', '<=', $fecha))
            ->when($sinRealizacion, fn($q) => $q->where('id', '!=', $sinRealizacion))
            ->get(['tipo_especie_id', 'serie', 'numero_inicio', 'numero_fin'])
            ->groupBy(fn($r) => $r->tipo_especie_id . '|' . $r->serie);

        return Lote::with('denominacion', 'compra', 'tipoEspecie')
            ->whereKey($entradas->keys())
            ->get()
            ->map(function (Lote $lote) use ($entradas, $salidas, $nulas, $reales) {
                $recibido = self::rangos($entradas->get($lote->id));
                $salido   = self::rangos($salidas->get($lote->id, []));
                $anulado  = self::rangos($nulas->get($lote->id, []));
                //las realizaciones solo guardan tipo+serie: la parte de este lote es la que cae en lo recibido
                $realizado = self::interseccion(
                    self::rangos($reales->get($lote->tipo_especie_id . '|' . $lote->serie, [])), $recibido);

                $intervalos = self::neto($recibido, array_merge($salido, $anulado, $realizado));
                $cantidad   = self::total($intervalos);
                $valor      = (float) $lote->denominacion->valor;

                return [
                    'lote'       => $lote,
                    'intervalos' => $intervalos,
                    'cantidad'   => $cantidad,
                    'valor'      => $valor,
                    'monto'      => $cantidad * $valor,
                    'recibido'   => self::total($recibido),
                    'salido'     => self::total($salido),
                    'anulado'    => self::total($anulado),
                    'realizado'  => self::total($realizado),
                ];
            })
            ->sort(fn($a, $b) => [$a['valor'], (string) $a['lote']->serie, $a['lote']->id]
                             <=> [$b['valor'], (string) $b['lote']->serie, $b['lote']->id])
            ->keyBy(fn($r) => $r['lote']->id);
    }

    //existencia de la bodega por lote a una fecha: lo comprado mas devoluciones menos envios.
    public static function bodega(?int $tipoId = null, ?Carbon $hasta = null): Collection
    {
        $fecha = $hasta?->toDateString();

        $movimientos = fn(string $tipo) => TrasladoDetalle::query()->toBase()
            ->join('traslados', 'traslados.id', '=', 'traslado_detalles.traslado_id')
            ->join('lotes', 'lotes.id', '=', 'traslado_detalles.lote_id')
            ->where('traslados.tipo', $tipo)
            ->when($tipoId, fn($q) => $q->where('lotes.tipo_especie_id', $tipoId))
            ->when($fecha, fn($q) => $q->where('traslados.fecha', '<=', $fecha))
            ->get(['traslado_detalles.lote_id', 'traslado_detalles.numero_inicio', 'traslado_detalles.numero_fin'])
            ->groupBy('lote_id');

        $enviados  = $movimientos('bodega_distrito');
        $devueltos = $movimientos('distrito_bodega');

        return Lote::with('rangos', 'denominacion', 'compra', 'tipoEspecie')
            ->when($tipoId, fn($q) => $q->where('tipo_especie_id', $tipoId))
            ->when($fecha, fn($q) => $q->whereHas('compra', fn($c) => $c->where('fecha', '<=', $fecha)))
            ->orderBy('tipo_especie_id')->orderBy('id')
            ->get()
            ->map(function (Lote $lote) use ($enviados, $devueltos) {
                $env = self::rangos($enviados->get($lote->id, []));
                $dev = self::rangos($devueltos->get($lote->id, []));

                $intervalos = self::neto(array_merge(self::rangos($lote->rangos), $dev), $env);
                $cantidad   = self::total($intervalos);
                $valor      = (float) $lote->denominacion->valor;

                return [
                    'lote'       => $lote,
                    'intervalos' => $intervalos,
                    'cantidad'   => $cantidad,
                    'valor'      => $valor,
                    'monto'      => $cantidad * $valor,
                    'enviado'    => self::total($env),
                    'devuelto'   => self::total($dev),
                ];
            })
            ->keyBy(fn($r) => $r['lote']->id);
    }

    //filas de saldo como las lista el libro: un rango vivo por fila, ordenado por valor (si el distrito
    //lo lleva asi) y orden de ingreso del lote.
    public static function filasSaldo(int $distritoId, int $tipoId, Carbon $hasta, ?int $denominacionId = null): array
    {
        $porValor = (bool) (Distrito::whereKey($distritoId)->value('saldo_por_valor') ?? true);
        $filas = [];
        foreach (self::distrito($distritoId, $tipoId, $hasta) as $r) {
            if ($r['cantidad'] <= 0 || ($denominacionId && $r['lote']->denominacion_id != $denominacionId)) continue;
            foreach ($r['intervalos'] as [$i, $f]) {
                $filas[] = ['lote' => $r['lote'], 'inicio' => $i, 'fin' => $f,
                            'cantidad' => $f - $i + 1, 'valor' => $r['valor'], 'monto' => ($f - $i + 1) * $r['valor']];
            }
        }
        usort($filas, fn($x, $y) => [$porValor ? $x['valor'] : 0, $x['lote']->id, $x['inicio']]
                                <=> [$porValor ? $y['valor'] : 0, $y['lote']->id, $y['inicio']]);
        return $filas;
    }

    //lotes de un tipo y serie que contienen alguna parte del rango.
    public static function lotesDelRango(int $tipoId, ?string $serie, int $inicio, int $fin): Collection
    {
        return Lote::with('rangos', 'denominacion')
            ->where('tipo_especie_id', $tipoId)
            ->where(fn($q) => $serie === null ? $q->whereNull('serie') : $q->where('serie', $serie))
            ->whereHas('rangos', fn($q) => $q->where('numero_inicio', '<=', $fin)->where('numero_fin', '>=', $inicio))
            ->get();
    }

    //recorre en orden cronologico los movimientos de un lote en un poseedor (un distrito, o la
    //bodega si es null) y devuelve la primera inconsistencia: sacar documentos que no estaban
    //o recibir documentos que ya estaban. null si la historia es valida.
    public static function verificar(?int $distritoId, int $loteId): ?string
    {
        $lote = Lote::with('rangos', 'compra', 'tipoEspecie')->find($loteId);
        if (!$lote) return null;

        $propios = self::rangos($lote->rangos);
        $eventos = [];
        $agregar = function (string $fecha, int $signo, array $rango, string $que) use (&$eventos) {
            if ($rango) $eventos[] = compact('fecha', 'signo', 'rango', 'que');
        };

        $traslados = fn(string $columna, $valor) => TrasladoDetalle::query()->toBase()
            ->join('traslados', 'traslados.id', '=', 'traslado_detalles.traslado_id')
            ->where('traslado_detalles.lote_id', $lote->id)
            ->where("traslados.$columna", $valor)
            ->get(['traslados.id', 'traslados.fecha', 'traslado_detalles.numero_inicio', 'traslado_detalles.numero_fin']);

        if ($distritoId === null) {
            $quien = 'La bodega';
            $agregar($lote->compra->fecha->toDateString(), 1, $propios, 'la compra (factura ' . $lote->compra->numero_factura . ')');
            foreach ($traslados('tipo', 'distrito_bodega') as $t) $agregar($t->fecha, 1, self::rangos([$t]), "la devolución #{$t->id}");
            foreach ($traslados('tipo', 'bodega_distrito') as $t) $agregar($t->fecha, -1, self::rangos([$t]), "el traslado #{$t->id}");
        } else {
            $quien = 'El distrito ' . preg_replace('/^distrito\s+/i', '', (string) Distrito::find($distritoId)?->nombre);
            foreach ($traslados('distrito_id', $distritoId) as $t)        $agregar($t->fecha, 1, self::rangos([$t]), "el traslado #{$t->id}");
            foreach ($traslados('origen_distrito_id', $distritoId) as $t) $agregar($t->fecha, -1, self::rangos([$t]), "el traslado #{$t->id}");

            $nulas = Nula::query()->toBase()
                ->join('traslado_detalles', 'traslado_detalles.id', '=', 'nulas.traslado_detalle_id')
                ->where('traslado_detalles.lote_id', $lote->id)
                ->where('nulas.distrito_id', $distritoId)
                ->get(['nulas.fecha', 'nulas.numero_inicio', 'nulas.numero_fin']);
            foreach ($nulas as $n) $agregar($n->fecha, -1, self::rangos([$n]), 'la anulación');

            $reales = Realizacion::query()->toBase()
                ->where('distrito_id', $distritoId)
                ->where('tipo_especie_id', $lote->tipo_especie_id)
                ->where(fn($q) => $lote->serie === null ? $q->whereNull('serie') : $q->where('serie', $lote->serie))
                ->get(['fecha', 'numero_inicio', 'numero_fin']);
            foreach ($reales as $r) $agregar($r->fecha, -1, self::interseccion(self::rangos([$r]), $propios), 'la realización');
        }

        $que = $lote->tipoEspecie->nombre . ($lote->serie ? " serie {$lote->serie}" : '')
             . ', factura ' . $lote->compra->numero_factura;
        $numeros = fn(array $r) => (self::total($r) === 1 ? 'el número ' : 'los números ') . self::texto($r);

        //las fechas no tienen hora: cada dia se evalua como un bloque, sin suponer orden interno
        $tiene = [];
        foreach (collect($eventos)->sortBy('fecha')->groupBy('fecha') as $fecha => $delDia) {
            $mas   = array_merge($tiene, ...$delDia->where('signo', 1)->pluck('rango')->all());
            $menos = array_merge(...$delDia->where('signo', -1)->pluck('rango')->all() ?: [[]]);
            $dia   = Carbon::parse($fecha)->format('d/m/Y');

            if ($doble = self::neto($mas, $menos, 2)) {
                $e = $delDia->where('signo', 1)->first(fn($e) => self::interseccion($e['rango'], $doble));
                return "{$quien} ya tenía " . $numeros(self::interseccion($doble, $e['rango']))
                     . " ({$que}) cuando llegó {$e['que']} el {$dia}.";
            }
            if ($falta = self::neto($menos, $mas)) {
                $e = $delDia->where('signo', -1)->first(fn($e) => self::interseccion($e['rango'], $falta));
                $faltan = self::interseccion($falta, $e['rango']);
                return "{$quien} no tenía " . (self::total($faltan) === 1 ? 'disponible ' : 'disponibles ') . $numeros($faltan)
                     . " ({$que}) el {$dia} para {$e['que']}.";
            }
            $tiene = self::neto($mas, $menos);
        }
        return null;
    }

    //verifica los pares [distrito|null, lote_id] y aborta con el primer error.
    //dentro de DB::transaction la excepcion deshace el cambio recien escrito.
    public static function asegurar(array $pares, string $campo = 'numero_inicio'): void
    {
        $vistos = [];
        foreach ($pares as [$distritoId, $loteId]) {
            $clave = ($distritoId ?? 'b') . '-' . $loteId;
            if (isset($vistos[$clave])) continue;
            $vistos[$clave] = true;

            if ($error = self::verificar($distritoId, $loteId)) {
                throw ValidationException::withMessages([$campo => $error]);
            }
        }
    }
}
