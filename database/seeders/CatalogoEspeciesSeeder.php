<?php

namespace Database\Seeders;

use App\Models\Denominacion;
use App\Models\Distrito;
use App\Models\DistritoDenominacion;
use App\Models\DistritoTipoEspecie;
use App\Models\TipoEspecie;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

//configuracion de libros y reportes tomada de los libros y reportes mensuales de tesoreria (nov-2025 a jun-2026):
//como se lleva cada tipo y, por distrito, sus rotulos, orden, costos y precios de venta.
//se puede correr varias veces: actualiza lo que ya existe.
class CatalogoEspeciesSeeder extends Seeder
{
    //tipo => [se lleva en unidades, orden]
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

    //costo general de cada denominacion (tipo => [[valor, costo]]); los distritos pueden tener el suyo
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

    //se venden a su valor facial: vialidad, tiquetes y bascula
    private const VENTA_AL_VALOR = ['Fondo Vialidad', 'Tiquetes de Mercado', 'Tiquetes Servicio de Báscula'];

    //crea o actualiza tipos, denominaciones, costos y la plantilla de cada distrito
    public function run(): void
    {
        foreach (self::TIPOS as $nombre => [$unidades, $orden]) {
            $tipo = TipoEspecie::firstOrCreate(['nombre' => $nombre], ['activo' => $nombre !== 'Cartas de Venta']);
            $tipo->update(['unidades' => $unidades, 'orden' => $orden]);
        }

        //cartas manuales: masahuat las realizo hasta nov-2025
        TipoEspecie::where('nombre', 'Cartas de Venta')->whereNull('descripcion')
            ->update(['descripcion' => 'Cartas de venta manuales, ya no se realizan']);
        //texistepeque sigue realizando carnet de menores
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
            //los distritos nuevos se crean aqui; los cuatro originales vienen de EspeciesMunicipalesSeeder
            $distrito = Distrito::firstOrCreate(['codigo' => $codigo], ['nombre' => $p['nombre'] ?? $codigo, 'activo' => true]);
            $distrito->update(['nombre_oficial' => $p['nombre_oficial'], 'lugar_firma' => $p['lugar_firma'] ?? null,
                               'saldo_por_valor' => $p['saldo_por_valor'] ?? true]);

            $ordenTipo = 0;
            foreach ($p['tipos'] as $tipo => $plantilla) {
                [$tituloLibro, $tituloReporte, $filas] = $plantilla;
                //el reporte de realizaciones usa los rotulos del control de saldos si no trae los suyos
                $tituloReal = $plantilla[3] ?? $tituloReporte;
                $filasReal  = $plantilla[4] ?? array_values(array_filter($filas, fn($f) => !$f['nulas']));

                DistritoTipoEspecie::updateOrCreate(
                    ['distrito_id' => $distrito->id, 'tipo_especie_id' => $tipos[$tipo]],
                    ['titulo_libro' => $tituloLibro, 'titulo_reporte' => $tituloReporte, 'titulo_realizaciones' => $tituloReal,
                     'orden' => ++$ordenTipo, 'nulas_en_realizaciones' => in_array($tipo, $p['nulas_en_realizaciones'] ?? [])]);

                //las filas van en el orden del reporte; la de nulas se guarda en la tarifa de su denominacion
                $tarifas = [];
                $tarifa  = function (float $valor) use (&$tarifas, $tipos, $tipo, $distrito) {
                    $den = Denominacion::where('tipo_especie_id', $tipos[$tipo])->where('valor', $valor)->firstOrFail();
                    $tarifas[$den->id] ??= ['distrito_id' => $distrito->id, 'denominacion_id' => $den->id,
                        'descripcion' => null, 'precio_costo' => null, 'precio_venta' => null, 'orden' => 0,
                        'descripcion_nulas' => null, 'orden_nulas' => null, 'nulas_con_venta' => false,
                        'descripcion_realizaciones' => null, 'orden_realizaciones' => null,
                        'nulas_realizaciones' => null, 'orden_nulas_realizaciones' => null];
                    return $den->id;
                };
                foreach ($filas as $orden => $f) {
                    $id = $tarifa($f['valor']);
                    if ($f['nulas']) {
                        $tarifas[$id] = array_merge($tarifas[$id], ['descripcion_nulas' => $f['descripcion'],
                            'orden_nulas' => $orden + 1, 'nulas_con_venta' => $f['con_venta']]);
                    } else {
                        $tarifas[$id] = array_merge($tarifas[$id], ['descripcion' => $f['descripcion'],
                            'precio_costo' => $f['costo'], 'precio_venta' => $f['venta'], 'orden' => $orden + 1]);
                    }
                }
                foreach ($filasReal as $orden => $f) {
                    $id = $tarifa($f['valor']);
                    $tarifas[$id] = array_merge($tarifas[$id], $f['nulas']
                        ? ['nulas_realizaciones' => $f['descripcion'], 'orden_nulas_realizaciones' => $orden + 1]
                        : ['descripcion_realizaciones' => $f['descripcion'], 'orden_realizaciones' => $orden + 1]);
                }
                foreach ($tarifas as $t) {
                    DistritoDenominacion::updateOrCreate(
                        ['distrito_id' => $t['distrito_id'], 'denominacion_id' => $t['denominacion_id']], $t);
                }
            }

        }
    }

    //plantilla de cada distrito, igual a sus libros, a su "Control de Saldos" (hojas SALDOS) y a sus hojas REALIZADAS.
    private function plantillas(): array
    {
        //fila del reporte: descripcion, valor de la denominacion, costo y precio de venta (null: no se vende)
        $fila  = fn(float $valor, string $descripcion, float $costo, ?float $venta = null) =>
            ['nulas' => false, 'valor' => $valor, 'descripcion' => $descripcion, 'costo' => $costo, 'venta' => $venta];
        //fila de nulas de esa denominacion (en las hojas de saldos siempre va en cero); con venta muestra 0.00
        $nulas = fn(float $valor, string $descripcion = 'NULAS', bool $conVenta = false) =>
            ['nulas' => true, 'valor' => $valor, 'descripcion' => $descripcion, 'con_venta' => $conVenta];
        //reporte de realizaciones (hojas REALIZADAS): titulo y filas cuando no son los del control de saldos;
        //la fila de nulas cuenta las anuladas de su denominacion, sin ella se suman a la fila de la denominacion
        $real      = fn(float $valor, string $descripcion) => ['nulas' => false, 'valor' => $valor, 'descripcion' => $descripcion];
        $nulasReal = fn(float $valor, string $descripcion = 'NULAS') => ['nulas' => true, 'valor' => $valor, 'descripcion' => $descripcion];
        $conNulas  = fn(float $valor, string $descripcion) => [$real($valor, $descripcion), $nulasReal($valor)];

        $titulos  = ['TITULOS A PERPETUIDAD', 'TITULOS A PERPETUIDAD EN CEMENTERIOS', [$fila(0.53, 'TITULOS', 0.53)]];
        $guias    = ['GUIAS DE CONDUCCION DE GANADO MAYOR Y MENOR', 'GUIAS DE CONDUCCION DE GANADO MAYOR Y MENOR',
                     [$fila(0.20, 'GUIAS DE CONDUCCION DE GANADO', 0.20)]];
        $continua = [$fila(0.10, 'FÓRMULAS 1-ISAM CONTINUAS', 0.10)];
        $vialidad343 = [$fila(3.43, 'DE 30 COLONES $ 3.43', 0.10, 3.43), $nulas(3.43, 'DE $3.43 NULAS', true)];
        $continuaReal    = ['FORMULAS 1-I SAM FORMA CONTINUA', [$real(0.10, 'FÓRMULAS 1-ISAM CONTINUA')]];
        $vialidad343Real = ['FONDO DE VIALIDAD', [$real(3.43, 'DE $ 3.43'), $nulasReal(3.43, 'DE $3.43 NULAS')]];

        return [
            'D01' => [
                'nombre_oficial' => 'DISTRITO DE METAPÁN',
                'lugar_firma'    => 'UNIDAD DE TESORERIA DISTRITO DE METAPAN',
                //libros que suman las nulas al total de realizaciones
                'nulas_en_realizaciones' => ['Cartas de Venta Continua', 'Tarjetas para Carnet de Identificación'],
                'tipos'          => [
                    'Fondo Vialidad' => ['FONDO DE VIALIDAD', 'FONDO DE VIALIDAD', [
                        $fila(0.57, 'DE 5 COLONES $0.57', 0.075, 0.57), $fila(1.14, 'DE $1.14', 0.15, 1.14),
                        $fila(3.43, 'DE 30 COLONES $ 3.43', 0.10, 3.43),
                    ], 'FONDO DE VIALIDAD', [
                        $real(0.57, 'DE $0.57'), $real(1.14, 'DE $1.14'), $real(3.43, 'DE $ 3.43'),
                        $nulasReal(0.57, 'DE $0.57 NULAS'), $nulasReal(1.14, 'DE $1.14 NULAS'), $nulasReal(3.43, 'DE $3.43 NULAS'),
                    ]],
                    'Tiquetes de Mercado' => ['TIQUETES DE MERCADO', 'TIQUETES DE MERCADO', [
                        $fila(0.12, 'DE $0.12', 0.0105, 0.12), $fila(0.15, 'DE $0.15', 0.015, 0.15),
                        $fila(0.50, 'DE $0.50', 0.015, 0.50), $fila(1.00, 'DE $1.00', 0.015, 1.00),
                    ]],
                    'Tiquetes Servicio de Báscula' => ['TIQUETES SERVICIO DE BASCULA', 'TIQUETES SERVICIO DE BASCULA', [
                        $fila(0.50, 'DE $0.50', 0.015, 0.50),
                    ]],
                    'Cartas de Venta Continua' => ['CARTAS DE VENTA', 'CARTAS DE VENTA', [
                        $fila(0.15, 'CARTAS DE VENTA', 0.15),
                    ], 'CARTAS DE VENTA', $conNulas(0.15, 'CARTAS DE VENTA')],
                    'Tarjetas para Carnet de Identificación' => ['TARJETAS PARA CARNET DE IDENTIFICACIÓN', 'TARJETAS CARNET DE MENORES', [
                        $fila(0.30, 'DE $3.15', 0.30, 3.15),
                    ], 'TARJETAS PARA CARNET DE IDENTIFICACIÓN', $conNulas(0.30, 'DE $3.15')],
                    'Fórmulas 1-ISAM Continua' => ['FORMULAS 1-ISAM', 'FORMULAS 1-I SAM', [
                        $fila(0.10, 'FÓRMULAS CONTINUAS', 0.10),
                    ]],
                    'Títulos a Perpetuidad'         => $titulos,
                    'Guías de Conducción de Ganado' => $guias,
                ],
            ],
            'D02' => [
                'nombre_oficial' => 'DISTRITO DE MASAHUAT',
                'nulas_en_realizaciones' => ['Cartas de Venta', 'Cartas de Venta Continua', 'Fórmulas 1-ISAM Continua', 'Guías de Conducción de Ganado'],
                'tipos'          => [
                    'Fondo Vialidad' => ['FONDO DE VIALIDAD', 'FONDO DE VIALIDAD', [
                        $fila(0.57, 'DE 5 COLONES $0.57', 0.10, 0.57), $fila(3.43, 'DE 30 COLONES $ 3.43', 0.10, 3.43),
                        $nulas(0.57, 'DE $0.57 NULAS', true), $nulas(3.43, 'DE $3.43 NULAS', true),
                    ], 'FONDO DE VIALIDAD', [
                        $real(0.57, 'DE $0.57'), $real(3.43, 'DE $ 3.43'), $nulasReal(3.43, 'DE $3.43 NULAS'), $nulasReal(0.57, 'DE $0.57 NULAS'),
                    ]],
                    'Cartas de Venta' => ['CARTAS DE VENTA', 'CARTAS DE VENTA', [
                        $fila(0.15, 'CARTAS DE VENTA', 0.15),
                    ], 'CARTAS DE VENTA', $conNulas(0.15, 'CARTAS DE VENTA')],
                    'Cartas de Venta Continua' => ['CARTA DE VENTA CONTINUA', 'CARTAS DE VENTA CONTINUAS', [
                        $fila(0.15, 'CARTAS DE VENTA', 0.15),
                    ], 'CARTAS DE VENTA CONTINUAS', $conNulas(0.15, 'CARTAS DE VENTA')],
                    'Tarjetas para Carnet de Identificación' => ['TARJETAS PARA CARNET DE IDENTIFICACIÓN', 'TARJETAS PARA CARNET DE IDENTIFICACION', [
                        $fila(0.30, 'TARJETAS PARA IDENTIFICACION', 0.30),
                    ], 'TARJETAS PARA CARNET DE IDENTIFICACIÓN', $conNulas(0.30, 'TARJETAS PARA IDENTIFICACION')],
                    'Fórmulas 1-ISAM Plana' => ['FORMULAS 1-ISAM FORMA PLANA', 'FORMULAS 1-I SAM FORMA PLANA', [
                        $fila(0.06, 'FÓRMULAS 1-ISAM FORMA PLANA', 0.06),
                    ]],
                    'Fórmulas 1-ISAM Continua' => ['FORMULAS 1-ISAM CONTINUA', 'FORMULAS 1-I SAM CONTINUA', $continua, ...$continuaReal],
                    'Títulos a Perpetuidad' => $titulos,
                    'Boletos de Cobro de Material Pétreo' => ['BOLETOS DE COBRO DE MATERIAL PETREO', 'BOLETOS DE COBRO DE MATERIAL PETREO', [
                        $fila(0.06, 'MATERIAL PETREO', 0.06),
                    ]],
                    'Tarjetas de Inmuebles' => ['TARJETAS DE INMUEBLES', 'TARJETAS DE INMUEBLES', [
                        $fila(0.26, 'TARJETAS DE INMUEBLES', 0.26),
                    ]],
                    'Tarjetas de Empresa' => ['TARJETAS DE EMPRESA', 'TARJETAS DE EMPRESAS', [
                        $fila(0.26, 'TARJETAS DE EMPRESAS', 0.26),
                    ], 'TARJETAS DE EMPRESA', [$real(0.26, 'TARJETAS DE EMPRESA')]],
                    'Guías de Conducción de Ganado' => $guias,
                ],
            ],
            'D03' => [
                'nombre_oficial' => 'DISTRITO DE SANTA ROSA GUACHIPILIN',
                'nulas_en_realizaciones' => ['Cartas de Venta Continua', 'Títulos a Perpetuidad', 'Guías de Conducción de Ganado'],
                'tipos'          => [
                    'Fondo Vialidad' => ['FONDO DE VIALIDAD', 'FONDO DE VIALIDAD', $vialidad343, ...$vialidad343Real],
                    'Tiquetes de Mercado' => ['TIQUETES DE MERCADO', 'TIQUETES DE MERCADO', [
                        $fila(0.15, 'DE $0.15', 0.015, 0.15), $fila(1.00, 'DE $1.00', 0.015, 1.00),
                    ]],
                    'Cartas de Venta Continua' => ['CARTAS DE VENTA', 'CARTAS DE VENTA', [
                        $fila(0.15, 'CARTAS DE VENTA', 0.15),
                    ], 'CARTAS DE VENTA', $conNulas(0.15, 'CARTAS DE VENTA')],
                    'Fórmulas 1-ISAM Continua' => ['FORMULAS 1-ISAM', 'FORMULAS 1-I SAM CONTINUA', $continua, ...$continuaReal],
                    'Títulos a Perpetuidad'         => $titulos,
                    'Guías de Conducción de Ganado' => $guias,
                    'Tarjetas para Carnet de Identificación' => ['TARJETAS PARA CARNET DE IDENTIFICACIÓN', 'TARJETAS PARA CARNET DE IDENTIFICACIÓN', [
                        $fila(0.30, 'TARJETAS PARA IDENTIFICACION', 0.30),
                    ]],
                ],
            ],
            'D04' => [
                'nombre_oficial'  => 'DISTRITO DE TEXISTEPEQUE',
                'saldo_por_valor' => false,   //su libro agrega lo que entra al final del saldo
                'nulas_en_realizaciones' => ['Cartas de Venta Continua', 'Tarjetas para Carnet de Identificación', 'Guías de Conducción de Ganado'],
                'tipos'           => [
                    'Fondo Vialidad' => ['FONDO DE VIALIDAD', 'FONDO DE VIALIDAD', $vialidad343, ...$vialidad343Real],
                    'Tiquetes de Mercado' => ['TIQUETES DE MERCADO', 'TIQUETES DE MERCADO', [
                        $fila(0.25, 'DE $0.25', 0.015, 0.25), $fila(1.00, 'DE $1.00', 0.015, 1.00),
                    ]],
                    'Tarjetas de Control de Mercado' => ['TARJETAS DE CONTROL DE MERCADO', 'TARJETAS DE CONTROL DE MERCADO', [
                        $fila(0.30, 'TARJETAS DE CONTROL DE MERCADO', 0.30),
                    ], 'TARJETAS DE CONTROL DE MERCADO', $conNulas(0.30, 'TARJETAS DE CONTROL DE MERCADO')],
                    'Cartas de Venta Continua' => ['CARTAS DE VENTA FORMA CONTINUA', 'CARTA DE VENTA CONTINUA', [
                        $fila(0.15, 'CARTAS DE VENTA', 0.15),
                    ], 'CARTA DE VENTA CONTINUA', $conNulas(0.15, 'CARTAS DE VENTA')],
                    'Carnet de Menores' => ['CARNET DE MENORES', 'CARNET DE IDENTIDAD DE MENORES', [
                        $fila(0.09, 'JUEGO DE CARTULINA', 0.09, 2.00), $fila(0.06, 'JUEGO DE CARTULINA', 0.06),
                    ], 'CARNET DE IDENTIDAD DE MENORES', [...$conNulas(0.09, 'JUEGO DE CARTULINA'), ...$conNulas(0.06, 'JUEGO DE CARTULINA')]],
                    'Tarjetas para Carnet de Identificación' => ['TARJETAS PARA CARNET DE IDENTIFICACIÓN', 'TARJETAS PARA CARNET DE IDENTIFICACION', [
                        $fila(0.30, 'TARJETAS PARA IDENTIFICACION', 0.30, 5.00),
                    ], 'TARJETA PARA CARNET DE IDENTIFICACION', $conNulas(0.30, 'TARJETAS PARA IDENTIFICACION')],
                    'Avisos de Cobro Común' => ['AVISOS DE COBRO COMUN', 'AVISOS DE COBRO COMUN', [
                        $fila(0.10, 'AVISOS DE COBRO COMUN', 0.10),
                    ]],
                    'Fórmulas 1-ISAM Continua' => ['FORMULAS 1-ISAM', 'FORMULAS 1-I SAM CONTINUA', $continua, ...$continuaReal],
                    'Títulos a Perpetuidad' => $titulos,
                    'Guías de Conducción de Ganado' => $guias,
                ],
            ],
            'D05' => [
                'nombre'         => 'Distrito Santa Ana Norte',
                'nombre_oficial' => 'DISTRITO DE SANTA ANA NORTE',
                'tipos'          => [
                    'Fórmulas 1-ISAM Continua' => ['FORMULAS 1-ISAM', 'FORMULAS 1-I SAM', [
                        $fila(0.10, 'FÓRMULAS CONTINUAS', 0.10),
                    ]],
                ],
            ],
        ];
    }
}
