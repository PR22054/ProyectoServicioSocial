<?php

namespace Database\Seeders;

use App\Models\Denominacion;
use App\Models\Distrito;
use App\Models\DistritoDenominacion;
use App\Models\DistritoTipoEspecie;
use App\Models\TipoEspecie;
use App\Models\Ubicacion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Configuracion de libros y reportes tomada de los libros y reportes mensuales de Tesoreria (nov-2025 a mar-2026):
 * como se lleva cada tipo y, por distrito, sus rotulos, orden, costos, precios de venta y cajas.
 * Se puede correr varias veces: actualiza lo que ya existe.
 */
class CatalogoEspeciesSeeder extends Seeder
{
    // tipo => [se lleva en unidades, orden]
    private const TIPOS = [
        'Fondo Vialidad'                         => [false, 1],
        'Tiquetes de Mercado'                    => [false, 2],
        'Tiquetes Servicio de Báscula'           => [false, 3],
        'Cartas de Venta'                        => [true, 4],
        'Cartas de Venta Continua'               => [true, 5],
        'Tarjetas para Carnet de Identificación' => [false, 6],
        'Carnet de Menores'                      => [false, 7],
        'Fórmulas 1-ISAM Plana'                  => [true, 8],
        'Fórmulas 1-ISAM Continua'               => [true, 9],
        'Títulos a Perpetuidad'                  => [false, 10],
        'Guías de Conducción de Ganado'          => [true, 11],
        'Boletos de Cobro de Material Pétreo'    => [false, 12],
        'Tarjetas de Inmuebles'                  => [false, 13],
        'Tarjetas de Empresa'                    => [false, 14],
        'Avisos de Cobro Común'                  => [false, 15],
        'Tarjetas de Control de Mercado'         => [true, 16],
        'Caja Auxiliar'                          => [false, 17],
    ];

    // Costo general de cada denominacion (tipo => [[valor, costo]]); los distritos pueden tener el suyo
    private const COSTOS = [
        'Fondo Vialidad'                         => [[0.57, 0.10], [1.14, 0.15], [3.43, 0.10]],
        'Tiquetes de Mercado'                    => [[0.12, 0.0105], [0.15, 0.015], [0.25, 0.015], [0.50, 0.015], [1.00, 0.015]],
        'Tiquetes Servicio de Báscula'           => [[0.50, 0.015]],
        'Cartas de Venta'                        => [[0.15, 0.15]],
        'Cartas de Venta Continua'               => [[0.15, 0.15]],
        'Tarjetas para Carnet de Identificación' => [[0.30, 0.30]],
        'Carnet de Menores'                      => [[0.06, 0.06], [0.09, 0.09]],
        'Fórmulas 1-ISAM Plana'                  => [[0.06, 0.06]],
        'Fórmulas 1-ISAM Continua'               => [[0.10, 0.10]],
        'Títulos a Perpetuidad'                  => [[0.53, 0.53]],
        'Guías de Conducción de Ganado'          => [[0.20, 0.20]],
        'Boletos de Cobro de Material Pétreo'    => [[0.06, 0.06]],
        'Tarjetas de Inmuebles'                  => [[0.26, 0.26]],
        'Tarjetas de Empresa'                    => [[0.26, 0.26]],
        'Avisos de Cobro Común'                  => [[0.10, 0.10]],
        'Tarjetas de Control de Mercado'         => [[0.30, 0.30]],
    ];

    // Se venden a su valor facial: vialidad, tiquetes y bascula
    private const VENTA_AL_VALOR = ['Fondo Vialidad', 'Tiquetes de Mercado', 'Tiquetes Servicio de Báscula'];

    public function run(): void
    {
        foreach (self::TIPOS as $nombre => [$unidades, $orden]) {
            $tipo = TipoEspecie::firstOrCreate(['nombre' => $nombre], ['activo' => $nombre !== 'Cartas de Venta']);
            $tipo->update(['unidades' => $unidades, 'orden' => $orden]);
        }

        // Cartas manuales: Masahuat las realizo hasta nov-2025
        TipoEspecie::where('nombre', 'Cartas de Venta')->whereNull('descripcion')
            ->update(['descripcion' => 'Cartas de venta manuales, ya no se realizan']);
        // Texistepeque sigue realizando carnet de menores
        $carnet = TipoEspecie::where('nombre', 'Carnet de Menores')->first();
        $carnet?->update(['activo' => true, 'descripcion' => 'Ya no se realizan en Metapán']);
        Denominacion::where('tipo_especie_id', $carnet?->id)->update(['activo' => true]);

        $tipos = TipoEspecie::pluck('id', 'nombre');
        foreach (self::COSTOS as $tipo => $costos) {
            foreach ($costos as [$valor, $costo]) {
                $den = Denominacion::firstOrCreate(['tipo_especie_id' => $tipos[$tipo], 'valor' => $valor], ['activo' => true]);
                if ($den->precio_costo === null) $den->update(['precio_costo' => $costo]);
            }
        }
        Denominacion::whereIn('tipo_especie_id', $tipos->only(self::VENTA_AL_VALOR)->values())
            ->whereNull('precio_venta')->update(['precio_venta' => DB::raw('valor')]);

        foreach ($this->plantillas() as $codigo => $p) {
            $distrito = Distrito::where('codigo', $codigo)->firstOrFail();
            $distrito->update(['nombre_oficial' => $p['nombre_oficial'], 'lugar_firma' => $p['lugar_firma'] ?? null,
                               'saldo_por_valor' => $p['saldo_por_valor'] ?? true]);

            $ordenTipo = 0;
            foreach ($p['tipos'] as $tipo => [$tituloLibro, $tituloReporte, $filas]) {
                DistritoTipoEspecie::updateOrCreate(
                    ['distrito_id' => $distrito->id, 'tipo_especie_id' => $tipos[$tipo]],
                    ['titulo_libro' => $tituloLibro, 'titulo_reporte' => $tituloReporte, 'orden' => ++$ordenTipo,
                     'nulas_en_realizaciones' => in_array($tipo, $p['nulas_en_realizaciones'] ?? [])]);

                // Las filas van en el orden del reporte; la de nulas se guarda en la tarifa de su denominacion
                $tarifas = [];
                foreach ($filas as $orden => $f) {
                    $den = Denominacion::where('tipo_especie_id', $tipos[$tipo])->where('valor', $f['valor'])->firstOrFail();
                    $tarifas[$den->id] ??= ['distrito_id' => $distrito->id, 'denominacion_id' => $den->id,
                                            'descripcion_nulas' => null, 'orden_nulas' => null, 'nulas_con_venta' => false];
                    if ($f['nulas']) {
                        $tarifas[$den->id] += ['descripcion' => null, 'precio_costo' => null, 'precio_venta' => null, 'orden' => 0];
                        $tarifas[$den->id]['descripcion_nulas'] = $f['descripcion'];
                        $tarifas[$den->id]['orden_nulas']       = $orden + 1;
                        $tarifas[$den->id]['nulas_con_venta']   = $f['con_venta'];
                    } else {
                        $tarifas[$den->id] = array_merge($tarifas[$den->id], ['descripcion' => $f['descripcion'],
                            'precio_costo' => $f['costo'], 'precio_venta' => $f['venta'], 'orden' => $orden + 1]);
                    }
                }
                foreach ($tarifas as $t) {
                    DistritoDenominacion::updateOrCreate(
                        ['distrito_id' => $t['distrito_id'], 'denominacion_id' => $t['denominacion_id']], $t);
                }
            }

            foreach ($p['cajas'] ?? [] as $orden => $caja) {
                Ubicacion::updateOrCreate(['distrito_id' => $distrito->id, 'nombre' => $caja], ['orden' => $orden + 1]);
            }
        }
    }

    /** Plantilla de cada distrito, igual a sus libros y a su reporte "Especies Municipales Realizadas". */
    private function plantillas(): array
    {
        // fila del reporte: descripcion, valor de la denominacion, costo y precio de venta (null: no se vende)
        $fila  = fn(float $valor, string $descripcion, float $costo, ?float $venta = null) =>
            ['nulas' => false, 'valor' => $valor, 'descripcion' => $descripcion, 'costo' => $costo, 'venta' => $venta];
        // fila de nulas de esa denominacion; con venta muestra 0.00 en PRECIO DE VTA.
        $nulas = fn(float $valor, string $descripcion = 'NULAS', bool $conVenta = false) =>
            ['nulas' => true, 'valor' => $valor, 'descripcion' => $descripcion, 'con_venta' => $conVenta];

        $titulos = ['TITULOS A PERPETUIDAD', 'TITULOS A PERPETUIDAD EN CEMENTERIOS', [$fila(0.53, 'TITULOS', 0.53)]];
        $guias   = ['GUIAS DE CONDUCCION DE GANADO MAYOR Y MENOR', 'GUIAS DE CONDUCCION DE GANADO MAYOR Y MENOR',
                    [$fila(0.20, 'GUIAS DE CONDUCCION DE GANADO', 0.20)]];
        $vialidad343 = [$fila(3.43, 'DE $ 3.43', 0.10, 3.43), $nulas(3.43, 'DE $3.43 NULAS', true)];

        return [
            'D01' => [
                'nombre_oficial' => 'DISTRITO DE METAPÁN',
                'lugar_firma'    => 'UNIDAD DE TESORERIA DISTRITO DE METAPAN',
                'cajas'          => ['CAJA 1', 'CAJA 2', 'RASTRO', 'AUXILIAR', 'C. DOMICILIO'],
                // libros que suman las nulas al total de realizaciones
                'nulas_en_realizaciones' => ['Cartas de Venta Continua', 'Tarjetas para Carnet de Identificación'],
                'tipos'          => [
                    'Fondo Vialidad' => ['FONDO DE VIALIDAD', 'FONDO DE VIALIDAD', [
                        $fila(0.57, 'DE $0.57', 0.075, 0.57), $fila(1.14, 'DE $1.14', 0.15, 1.14), $fila(3.43, 'DE $ 3.43', 0.10, 3.43),
                        $nulas(0.57, 'DE $0.57 NULAS', true), $nulas(1.14, 'DE $1.14 NULAS', true), $nulas(3.43, 'DE $3.43 NULAS', true),
                    ]],
                    'Tiquetes de Mercado' => ['TIQUETES DE MERCADO', 'TIQUETES DE MERCADO', [
                        $fila(0.12, 'DE $0.12', 0.0105, 0.12), $fila(0.15, 'DE $0.15', 0.015, 0.15),
                        $fila(0.50, 'DE $0.50', 0.015, 0.50), $fila(1.00, 'DE $1.00', 0.015, 1.00),
                    ]],
                    'Tiquetes Servicio de Báscula' => ['TIQUETES SERVICIO DE BASCULA', 'TIQUETES SERVICIO DE BASCULA', [
                        $fila(0.50, 'DE $0.50', 0.015, 0.50),
                    ]],
                    'Cartas de Venta Continua' => ['CARTAS DE VENTA', 'CARTAS DE VENTA', [
                        $fila(0.15, 'CARTAS DE VENTA', 0.15), $nulas(0.15),
                    ]],
                    'Tarjetas para Carnet de Identificación' => ['TARJETAS PARA CARNET DE IDENTIFICACIÓN', 'TARJETAS PARA CARNET DE IDENTIFICACIÓN', [
                        $fila(0.30, 'DE $3.15', 0.30, 3.15), $nulas(0.30, 'NULAS', true),
                    ]],
                    'Fórmulas 1-ISAM Continua' => ['FORMULAS 1-ISAM', 'FORMULAS 1-I SAM', [
                        $fila(0.10, 'FÓRMULAS CONTINUAS', 0.10),
                    ]],
                    'Títulos a Perpetuidad'         => $titulos,
                    'Guías de Conducción de Ganado' => $guias,
                ],
            ],
            'D02' => [
                'nombre_oficial' => 'DISTRITO DE MASAHUAT',
                'nulas_en_realizaciones' => ['Cartas de Venta', 'Cartas de Venta Continua', 'Fórmulas 1-ISAM Continua'],
                'tipos'          => [
                    'Fondo Vialidad' => ['FONDO DE VIALIDAD', 'FONDO DE VIALIDAD', [
                        $fila(0.57, 'DE $0.57', 0.10, 0.57), $fila(3.43, 'DE $ 3.43', 0.10, 3.43),
                        $nulas(3.43, 'DE $3.43 NULAS', true), $nulas(0.57, 'DE $0.57 NULAS', true),
                    ]],
                    'Cartas de Venta' => ['CARTAS DE VENTA', 'CARTAS DE VENTA', [
                        $fila(0.15, 'CARTAS DE VENTA', 0.15), $nulas(0.15),
                    ]],
                    'Cartas de Venta Continua' => ['CARTA DE VENTA CONTINUA', 'CARTAS DE VENTA CONTINUAS', [
                        $fila(0.15, 'CARTAS DE VENTA', 0.15), $nulas(0.15),
                    ]],
                    'Tarjetas para Carnet de Identificación' => ['TARJETAS PARA CARNET DE IDENTIFICACIÓN', 'TARJETAS PARA CARNET DE IDENTIFICACIÓN', [
                        $fila(0.30, 'TARJETAS PARA IDENTIFICACION', 0.30), $nulas(0.30, 'NULAS', true),
                    ]],
                    'Fórmulas 1-ISAM Plana' => ['FORMULAS 1-ISAM FORMA PLANA', 'FORMULAS 1-I SAM FORMA PLANA', [
                        $fila(0.06, 'FÓRMULAS 1-ISAM FORMA PLANA', 0.06),
                    ]],
                    'Fórmulas 1-ISAM Continua' => ['FORMULAS 1-ISAM CONTINUA', 'FORMULAS 1-I SAM FORMA CONTINUA', [
                        $fila(0.10, 'FÓRMULAS 1-ISAM CONTINUA', 0.10),
                    ]],
                    'Títulos a Perpetuidad' => $titulos,
                    'Boletos de Cobro de Material Pétreo' => ['BOLETOS DE COBRO DE MATERIAL PETREO', 'BOLETOS DE COBRO DE MATERIAL PETREO', [
                        $fila(0.06, 'MATERIAL PETREO', 0.06),
                    ]],
                    'Tarjetas de Inmuebles' => ['TARJETAS DE INMUEBLES', 'TARJETAS DE INMUEBLES', [
                        $fila(0.26, 'TARJETAS DE INMUEBLES', 0.26),
                    ]],
                    'Tarjetas de Empresa' => ['TARJETAS DE EMPRESA', 'TARJETAS DE EMPRESA', [
                        $fila(0.26, 'TARJETAS DE EMPRESA', 0.26),
                    ]],
                    'Guías de Conducción de Ganado' => $guias,
                ],
            ],
            'D03' => [
                'nombre_oficial' => 'DISTRITO DE SANTA ROSA GUACHIPILIN',
                'nulas_en_realizaciones' => ['Cartas de Venta Continua', 'Títulos a Perpetuidad'],
                'tipos'          => [
                    'Fondo Vialidad' => ['FONDO DE VIALIDAD', 'FONDO DE VIALIDAD', $vialidad343],
                    'Tiquetes de Mercado' => ['TIQUETES DE MERCADO', 'TIQUETES DE MERCADO', [
                        $fila(0.15, 'DE $0.15', 0.015), $fila(1.00, 'DE $1.00', 0.015),
                    ]],
                    'Cartas de Venta Continua' => ['CARTAS DE VENTA', 'CARTAS DE VENTA', [
                        $fila(0.15, 'CARTAS DE VENTA', 0.15), $nulas(0.15),
                    ]],
                    'Fórmulas 1-ISAM Continua' => ['FORMULAS 1-ISAM', 'FORMULAS 1-I SAM FORMA CONTINUA', [
                        $fila(0.10, 'FÓRMULAS 1-ISAM CONTINUA', 0.10),
                    ]],
                    'Títulos a Perpetuidad'         => $titulos,
                    'Guías de Conducción de Ganado' => $guias,
                ],
            ],
            'D04' => [
                'nombre_oficial'  => 'DISTRITO DE TEXISTEPEQUE',
                'saldo_por_valor' => false,   // su libro agrega lo que entra al final del saldo
                'nulas_en_realizaciones' => ['Cartas de Venta Continua'],
                'tipos'           => [
                    'Fondo Vialidad' => ['FONDO DE VIALIDAD', 'FONDO DE VIALIDAD', $vialidad343],
                    'Tiquetes de Mercado' => ['TIQUETES DE MERCADO', 'TIQUETES DE MERCADO', [
                        $fila(0.25, 'DE $0.25', 0.015, 0.25), $fila(1.00, 'DE $1.00', 0.015, 1.00),
                    ]],
                    'Tarjetas de Control de Mercado' => ['TARJETAS DE CONTROL DE MERCADO', 'TARJETAS DE CONTROL DE MERCADO', [
                        $fila(0.30, 'TARJETAS DE CONTROL DE MERCADO', 0.30), $nulas(0.30),
                    ]],
                    'Cartas de Venta Continua' => ['CARTAS DE VENTA FORMA CONTINUA', 'CARTA DE VENTA CONTINUA', [
                        $fila(0.15, 'CARTAS DE VENTA', 0.15), $nulas(0.15),
                    ]],
                    'Carnet de Menores' => ['CARNET DE MENORES', 'CARNET DE IDENTIDAD DE MENORES', [
                        $fila(0.09, 'JUEGO DE CARTULINA', 0.09, 2.00), $nulas(0.09),
                    ]],
                    'Avisos de Cobro Común' => ['AVISOS DE COBRO COMUN', 'AVISOS DE COBRO COMUN', [
                        $fila(0.10, 'AVISOS DE COBRO COMUN', 0.10),
                    ]],
                    'Fórmulas 1-ISAM Continua' => ['FORMULAS 1-ISAM', 'FORMULAS 1-I SAM FORMA CONTINUA', [
                        $fila(0.10, 'FÓRMULAS 1-ISAM CONTINUA', 0.10),
                    ]],
                    'Títulos a Perpetuidad' => $titulos,
                ],
            ],
        ];
    }
}
