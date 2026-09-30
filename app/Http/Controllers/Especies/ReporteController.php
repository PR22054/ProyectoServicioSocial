<?php

namespace App\Http\Controllers\Especies;

use App\Http\Controllers\Controller;
use App\Models\Denominacion;
use App\Models\Distrito;
use App\Models\DistritoDenominacion;
use App\Models\DistritoTipoEspecie;
use App\Models\LoteRango;
use App\Models\Nula;
use App\Models\Realizacion;
use App\Models\TipoEspecie;
use App\Models\TrasladoDetalle;
use App\Services\Inventario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;

class ReporteController extends Controller
{
    //CONTROLADOR DE REPORTES - genera PDF para los 6 tipos de reporte del modulo de especies municipales
    private array $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
    ];

    private function pdfConfig(): array
    {
        return ['margin_top' => 15, 'margin_bottom' => 15, 'margin_left' => 15, 'margin_right' => 15];
    }

    // ─── LIBRO DE ESPECIES ───────────────────────────────────────────────────
    public function libro(Request $request)
    {
        $distritos = Distrito::where('activo', true)->orderBy('codigo')->get();
        $tipos     = TipoEspecie::orderBy('nombre')->get();

        if ($request->has('generar')) {
            $request->validate([
                'distrito_id'     => 'required|exists:distritos,id',
                'tipo_especie_id' => 'required|exists:tipo_especies,id',
                'mes'             => 'required|integer|between:1,12',
                'anio'            => 'required|integer|min:2020',
            ]);
            return $this->pdfLibro($request);
        }

        return view('frontend.admin.especies.reportes.libro', compact('distritos', 'tipos'));
    }

    private function pdfLibro(Request $request)
    {
        $distrito    = Distrito::findOrFail($request->distrito_id);
        $tipo        = TipoEspecie::findOrFail($request->tipo_especie_id);
        $denomFiltro = $request->denominacion_id ? Denominacion::find($request->denominacion_id) : null;
        $libro       = $this->datosLibro($distrito, $tipo, (int) $request->mes, (int) $request->anio, $denomFiltro);

        $html = view('frontend.admin.especies.reportes.pdf.libro', $libro)->render();

        return PDF::loadHTML($html, $this->pdfConfig())
            ->stream("libro-{$distrito->codigo}-{$request->mes}-{$request->anio}.pdf");
    }

    /**
     * Libro de Especies Municipales (TESO-008) de un distrito, tipo y mes, en el orden y la forma de los
     * libros de Tesoreria: saldo anterior, compras por factura, traslados recibidos, realizaciones, nulas,
     * traslados enviados y saldo a nueva cuenta. Los tipos que se llevan en unidades no se valuan.
     */
    public function datosLibro(Distrito $distrito, TipoEspecie $tipo, int $mes, int $anio, ?Denominacion $denomFiltro = null): array
    {
        $inicioMes = Carbon::create($anio, $mes, 1)->startOfMonth();
        $finMes    = $inicioMes->copy()->endOfMonth();
        $unidades  = (bool) $tipo->unidades;
        $config    = DistritoTipoEspecie::where('distrito_id', $distrito->id)->where('tipo_especie_id', $tipo->id)->first();
        $cajas     = Inventario::cajas($distrito->id, $tipo->id, $finMes);

        $fila = fn(?string $caja, int $cant, float $valor, int $del, int $al, ?string $serie) => [
            'caja' => $caja, 'cantidad' => $cant, 'valor' => $valor, 'del' => $del, 'al' => $al, 'serie' => $serie,
            'monto' => $unidades ? $cant : $cant * $valor,
        ];
        $total = fn(array $filas) => array_sum(array_column($filas, 'monto'));

        // Con filtro de denominacion todas las secciones se acotan a ella, si no el libro no cuadra
        $loteFiltro = fn($q) => $q->where('tipo_especie_id', $tipo->id)
            ->when($denomFiltro, fn($q2) => $q2->where('denominacion_id', $denomFiltro->id));

        $saldo = fn(Carbon $hasta) => array_map(
            fn($r) => $fila($r['caja'], $r['cantidad'], $r['valor'], $r['inicio'], $r['fin'], $r['lote']->serie),
            Inventario::filasSaldo($distrito->id, $tipo->id, $hasta, $denomFiltro?->id));

        // Movimientos de traslado del mes, agrupados por traslado y factura como los asienta el libro
        $traslados = fn(string $columna) => TrasladoDetalle::with('traslado.distrito', 'traslado.origenDistrito', 'lote.compra', 'lote.denominacion')
            ->whereHas('traslado', fn($q) => $q->where($columna, $distrito->id)->whereBetween('fecha', [$inicioMes, $finMes]))
            ->whereHas('lote', $loteFiltro)
            ->get()
            ->sortBy(fn($d) => [$d->traslado->fecha->timestamp, $d->traslado_id, $d->id])
            ->groupBy(fn($d) => $d->traslado_id . '|' . $d->lote->compra_id)
            ->map(fn($ds) => [
                'traslado' => $ds->first()->traslado,
                'factura'  => $ds->first()->lote->compra->numero_factura ?? null,
                'filas'    => $ds->map(fn($d) => $fila(null, $d->cantidad, (float) $d->lote->denominacion->valor,
                                                       $d->numero_inicio, $d->numero_fin, $d->lote->serie))->values()->all(),
            ])->values();

        $entradas = $traslados('distrito_id');
        $salidas  = $traslados('origen_distrito_id');

        $realizaciones = Realizacion::with('denominacion')
            ->where('distrito_id', $distrito->id)->where('tipo_especie_id', $tipo->id)
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->when($denomFiltro, fn($q) => $q->where('denominacion_id', $denomFiltro->id))
            ->orderBy('fecha')->orderBy('id')->get()
            ->map(fn($r) => $fila(Inventario::cajaDe($cajas[(string) $r->serie] ?? [], $r->numero_inicio), $r->cantidad,
                                  (float) $r->denominacion->valor, $r->numero_inicio, $r->numero_fin, $r->serie))
            ->all();

        $nulas = Nula::with('trasladoDetalle.lote.denominacion')
            ->where('distrito_id', $distrito->id)
            ->whereHas('trasladoDetalle.lote', $loteFiltro)
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->orderBy('fecha')->orderBy('id')->get()
            ->map(fn($n) => $fila(Inventario::cajaDe($cajas[(string) $n->trasladoDetalle->lote->serie] ?? [], $n->numero_inicio),
                                  $n->numero_fin - $n->numero_inicio + 1, (float) $n->trasladoDetalle->lote->denominacion->valor,
                                  $n->numero_inicio, $n->numero_fin, $n->trasladoDetalle->lote->serie))
            ->all();

        $seccion = fn(string $tipoSec, ?Carbon $fecha, string $concepto, array $filas, ?string $factura = null) =>
            ['tipo' => $tipoSec, 'fecha' => $fecha, 'concepto' => $concepto, 'factura' => $factura, 'filas' => $filas, 'total' => $total($filas)];

        $secciones = [$seccion('inicio', $inicioMes, 'SALDO ANTERIOR', $saldo($inicioMes->copy()->subDay()))];

        // Lo que llega de bodega es la compra al M.H. del distrito; lo que llega de otro distrito, traslado
        $compras   = $entradas->filter(fn($g) => $g['traslado']->tipo === 'bodega_distrito');
        $recibidos = $entradas->reject(fn($g) => $g['traslado']->tipo === 'bodega_distrito');

        foreach ($compras as $g) $secciones[] = $seccion('compra', $g['traslado']->fecha, 'POR COMPRA AL M.H. FACTURA N°', $g['filas'], $g['factura']);
        if ($compras->isEmpty()) $secciones[] = $seccion('compra', null, 'POR COMPRA AL M.H. FACTURA N°', []);

        foreach ($recibidos as $g) $secciones[] = $seccion('entrada', $g['traslado']->fecha, 'POR TRASLADOS ' . $g['traslado']->origenDistrito->nombre_reporte, $g['filas']);
        if ($recibidos->isEmpty()) $secciones[] = $seccion('entrada', null, 'POR TRASLADOS', []);

        // Algunos libros suman las nulas al total de realizaciones; los demas las descuentan aparte
        $nulasJuntas = (bool) $config?->nulas_en_realizaciones;
        $real        = $seccion('realizacion', $finMes, 'POR REALIZACIONES EN EL MES', $realizaciones);
        if ($nulasJuntas) $real['total'] += $total($nulas);
        $secciones[] = $real;
        $secciones[] = $seccion('nulas', null, 'NULAS', $nulas);

        foreach ($salidas as $g) $secciones[] = $seccion('salida', $g['traslado']->fecha,
            'POR TRASLADOS ' . ($g['traslado']->distrito?->nombre_reporte ?? 'A BODEGA'), $g['filas']);
        if ($salidas->isEmpty()) $secciones[] = $seccion('salida', null, 'POR TRASLADOS', []);

        $secciones[] = $seccion('final', $finMes, 'POR SALDO A NUEVA CUENTA', $saldo($finMes));

        $nombreMes = $this->meses[$mes];

        return [
            'distrito'    => $distrito,
            'tipo'        => $tipo,
            'unidades'    => $unidades,
            'titulo'      => $config?->titulo_libro ?: mb_strtoupper($tipo->nombre),
            'mes'         => $mes,
            'anio'        => $anio,
            'nombreMes'   => $nombreMes,
            'denomFiltro' => $denomFiltro,
            'secciones'   => $secciones,
            'nulasJuntas' => $nulasJuntas,
            'pie'         => ($distrito->lugar_firma ?: $distrito->nombre_reporte) . ', '
                           . $finMes->day . ' DE ' . mb_strtoupper($nombreMes) . ' DE ' . $anio . '.',
            'firmas'      => config('especies.firmas'),
        ];
    }

    // ─── EXISTENCIAS EN BODEGA ───────────────────────────────────────────────
    public function bodega(Request $request)
    {
        $tipos = TipoEspecie::orderBy('nombre')->get();

        if ($request->has('generar')) {
            $request->validate([
                'tipo_especie_id' => 'required|exists:tipo_especies,id',
                'fecha_desde'     => 'required|date',
                'fecha_hasta'     => 'required|date|after_or_equal:fecha_desde',
            ]);
            return $this->pdfBodega($request);
        }

        return view('frontend.admin.especies.reportes.bodega', compact('tipos'));
    }

    private function pdfBodega(Request $request)
    {
        $tipo   = TipoEspecie::findOrFail($request->tipo_especie_id);
        $desde  = Carbon::parse($request->fecha_desde)->startOfDay();
        $hasta  = Carbon::parse($request->fecha_hasta)->endOfDay();

        // Lotes de compras registradas dentro del rango de fechas
        $rangos = LoteRango::whereHas('lote',
                fn($q) => $q->where('tipo_especie_id', $tipo->id)
                            ->whereHas('compra', fn($q2) => $q2->whereBetween('fecha', [$desde, $hasta])))
            ->with(['lote.compra', 'lote.denominacion'])
            ->orderBy('lote_id')->orderBy('numero_inicio')->get();

        // Enviado desde bodega (solo bodega_distrito reduce el stock de bodega)
        $enviado = TrasladoDetalle::whereHas('lote', fn($q) => $q->where('tipo_especie_id', $tipo->id))
            ->whereHas('traslado', fn($q) => $q->where('tipo', 'bodega_distrito')->where('fecha', '<=', $hasta))
            ->selectRaw('lote_id, SUM(cantidad) as total')
            ->groupBy('lote_id')->pluck('total', 'lote_id');

        // Devuelto a bodega (distrito_bodega aumenta el stock de bodega)
        $devuelto = TrasladoDetalle::whereHas('lote', fn($q) => $q->where('tipo_especie_id', $tipo->id))
            ->whereHas('traslado', fn($q) => $q->where('tipo', 'distrito_bodega')->where('fecha', '<=', $hasta))
            ->selectRaw('lote_id, SUM(cantidad) as total')
            ->groupBy('lote_id')->pluck('total', 'lote_id');

        $lotes = $rangos->groupBy('lote_id')->map(function ($rs) use ($enviado, $devuelto) {
            $lote        = $rs->first()->lote;
            $totalRangos = $rs->sum(fn($r) => $r->numero_fin - $r->numero_inicio + 1);
            $disponible  = max(0, $totalRangos - $enviado->get($lote->id, 0) + $devuelto->get($lote->id, 0));
            $valor       = (float) ($lote->denominacion->valor ?? 0);

            return compact('lote', 'disponible') + [
                'rangos'            => $rs,
                'total'             => $totalRangos,
                'valor'             => $valor,
                'monto_total'       => $totalRangos * $valor,
                'monto_trasladado'  => ($totalRangos - $disponible) * $valor,
                'monto_disponible'  => $disponible * $valor,
            ];
        })->filter(fn($l) => $l['disponible'] > 0);

        $html = view('frontend.admin.especies.reportes.pdf.bodega',
            compact('tipo', 'desde', 'hasta', 'lotes'))->render();

        return PDF::loadHTML($html, $this->pdfConfig())
            ->stream("bodega-{$tipo->id}-{$request->fecha_desde}-{$request->fecha_hasta}.pdf");
    }

    // ─── EXISTENCIAS POR DISTRITO ────────────────────────────────────────────
    public function distritos(Request $request)
    {
        $distritos = Distrito::where('activo', true)->orderBy('codigo')->get();
        $tipos     = TipoEspecie::orderBy('nombre')->get();

        if ($request->has('generar')) {
            $request->validate([
                'distrito_id'     => 'required|exists:distritos,id',
                'tipo_especie_id' => 'required|exists:tipo_especies,id',
                'fecha_corte'     => 'required|date',
            ]);
            return $this->pdfDistritos($request);
        }

        return view('frontend.admin.especies.reportes.distritos', compact('distritos', 'tipos'));
    }

    private function pdfDistritos(Request $request)
    {
        $distrito   = Distrito::findOrFail($request->distrito_id);
        $tipo       = TipoEspecie::findOrFail($request->tipo_especie_id);
        $fechaCorte = Carbon::parse($request->fecha_corte)->endOfDay();

        // Una fila por lote recibido: lo que queda al corte y en que rangos, acotado por serie
        $rows = Inventario::distrito($distrito->id, $tipo->id, $fechaCorte)->values();

        $html = view('frontend.admin.especies.reportes.pdf.distritos',
            compact('distrito', 'tipo', 'fechaCorte', 'rows'))->render();

        return PDF::loadHTML($html, $this->pdfConfig())
            ->stream("distrito-{$distrito->codigo}-{$request->fecha_corte}.pdf");
    }

    // ─── REALIZACIONES POR PERIODO ───────────────────────────────────────────
    public function realizaciones(Request $request)
    {
        $distritos = Distrito::where('activo', true)->orderBy('codigo')->get();
        $tipos     = TipoEspecie::orderBy('nombre')->get();

        if ($request->has('generar')) {
            $request->validate([
                'distrito_id'     => 'required|exists:distritos,id',
                'tipo_especie_id' => 'required|exists:tipo_especies,id',
                'fecha_desde'     => 'required|date',
                'fecha_hasta'     => 'required|date|after_or_equal:fecha_desde',
            ]);
            return $this->pdfRealizaciones($request);
        }

        return view('frontend.admin.especies.reportes.realizaciones', compact('distritos', 'tipos'));
    }

    private function pdfRealizaciones(Request $request)
    {
        $distrito = Distrito::findOrFail($request->distrito_id);
        $tipo     = TipoEspecie::findOrFail($request->tipo_especie_id);
        $desde    = Carbon::parse($request->fecha_desde)->startOfDay();
        $hasta    = Carbon::parse($request->fecha_hasta)->endOfDay();

        $realizaciones = Realizacion::where('distrito_id', $distrito->id)
            ->where('tipo_especie_id', $tipo->id)
            ->whereBetween('fecha', [$desde, $hasta])
            ->with(['denominacion', 'usuario'])
            ->orderBy('fecha')->orderBy('numero_inicio')->get();

        $html = view('frontend.admin.especies.reportes.pdf.realizaciones',
            compact('distrito', 'tipo', 'desde', 'hasta', 'realizaciones'))->render();

        return PDF::loadHTML($html, $this->pdfConfig())
            ->stream("realizaciones-{$distrito->codigo}.pdf");
    }

    // ─── HISTORIAL DE TRASLADOS ──────────────────────────────────────────────
    public function traslados(Request $request)
    {
        $distritos = Distrito::where('activo', true)->orderBy('codigo')->get();
        $tipos     = TipoEspecie::orderBy('nombre')->get();

        if ($request->has('generar')) {
            $request->validate([
                'distrito_id'     => 'required|exists:distritos,id',
                'tipo_especie_id' => 'required|exists:tipo_especies,id',
                'fecha_desde'     => 'required|date',
                'fecha_hasta'     => 'required|date|after_or_equal:fecha_desde',
            ]);
            return $this->pdfTraslados($request);
        }

        return view('frontend.admin.especies.reportes.traslados', compact('distritos', 'tipos'));
    }

    private function pdfTraslados(Request $request)
    {
        $distrito = Distrito::findOrFail($request->distrito_id);
        $tipo     = TipoEspecie::findOrFail($request->tipo_especie_id);
        $desde    = Carbon::parse($request->fecha_desde)->startOfDay();
        $hasta    = Carbon::parse($request->fecha_hasta)->endOfDay();

        // Entradas y salidas del distrito: lo recibido y lo devuelto a bodega o enviado a otro distrito
        $detalles = TrasladoDetalle::whereHas('traslado', fn($q) => $q
                ->where(fn($q2) => $q2->where('distrito_id', $distrito->id)->orWhere('origen_distrito_id', $distrito->id))
                ->whereBetween('fecha', [$desde, $hasta]))
            ->whereHas('lote', fn($q) => $q->where('tipo_especie_id', $tipo->id))
            ->with(['traslado.distrito', 'traslado.origenDistrito', 'lote.compra'])
            ->orderBy('traslado_id')->orderBy('numero_inicio')->get();

        $html = view('frontend.admin.especies.reportes.pdf.traslados',
            compact('distrito', 'tipo', 'desde', 'hasta', 'detalles'))->render();

        return PDF::loadHTML($html, $this->pdfConfig())
            ->stream("traslados-{$distrito->codigo}.pdf");
    }

    // ─── REPORTE MENSUAL ─────────────────────────────────────────────────────
    public function mensual(Request $request)
    {
        if ($request->has('generar')) {
            $request->validate([
                'mes'  => 'required|integer|between:1,12',
                'anio' => 'required|integer|min:2020',
            ]);
            return $this->pdfMensual($request);
        }

        return view('frontend.admin.especies.reportes.mensual');
    }

    private function pdfMensual(Request $request)
    {
        $mes       = (int) $request->mes;
        $anio      = (int) $request->anio;
        $nombreMes = $this->meses[$mes];

        $inicioMes = Carbon::create($anio, $mes, 1)->startOfMonth();
        $finMes    = $inicioMes->copy()->endOfMonth();

        // Fechas como texto Y-m-d en todos lados: comparar un Carbon contra texto
        // dejaba fuera los movimientos del ultimo dia del mes
        $ini = $inicioMes->toDateString();
        $fin = $finMes->toDateString();

        // Todos, no solo activos: un tipo desactivado puede tener saldo o movimiento en el mes
        $distritos = Distrito::orderBy('codigo')->get();
        $tipos     = TipoEspecie::orderBy('nombre')->get();

        // Cargar todos los datos en bulk para eficiencia
        // Solo traslados entrantes a distritos (bodega_distrito y distrito_distrito como destino)
        $allDetalles = TrasladoDetalle::query()->toBase()->join('traslados', 'traslado_detalles.traslado_id', '=', 'traslados.id')
            ->join('lotes', 'traslado_detalles.lote_id', '=', 'lotes.id')
            ->where('traslados.fecha', '<=', $finMes)
            ->whereNotNull('traslados.distrito_id')
            ->select('traslados.distrito_id', 'lotes.tipo_especie_id',
                     'traslados.fecha', 'traslado_detalles.cantidad')
            ->get();

        // Salidas desde distritos (devoluciones y traslados inter-distrito como origen)
        $allSalidas = TrasladoDetalle::query()->toBase()->join('traslados', 'traslado_detalles.traslado_id', '=', 'traslados.id')
            ->join('lotes', 'traslado_detalles.lote_id', '=', 'lotes.id')
            ->where('traslados.fecha', '<=', $finMes)
            ->whereIn('traslados.tipo', ['distrito_bodega', 'distrito_distrito'])
            ->whereNotNull('traslados.origen_distrito_id')
            ->select('traslados.origen_distrito_id as distrito_id', 'lotes.tipo_especie_id',
                     'traslados.fecha', 'traslado_detalles.cantidad')
            ->get();

        $allReal = Realizacion::query()->toBase()->where('fecha', '<=', $finMes)
            ->select('distrito_id', 'tipo_especie_id', 'fecha', 'cantidad', 'monto_cobrado')->get();

        $allNulas = Nula::query()->toBase()->join('traslado_detalles', 'nulas.traslado_detalle_id', '=', 'traslado_detalles.id')
            ->join('traslados', 'traslado_detalles.traslado_id', '=', 'traslados.id')
            ->join('lotes', 'traslado_detalles.lote_id', '=', 'lotes.id')
            ->where('nulas.fecha', '<=', $finMes)
            ->select('traslados.distrito_id', 'lotes.tipo_especie_id',
                     'nulas.fecha', \DB::raw('nulas.numero_fin - nulas.numero_inicio + 1 as cantidad'))
            ->get();

        $tabla = [];

        foreach ($distritos as $distrito) {
            foreach ($tipos as $tipo) {
                $key = "{$distrito->id}-{$tipo->id}";

                $recAntes  = $allDetalles->where('distrito_id', $distrito->id)
                    ->where('tipo_especie_id', $tipo->id)->where('fecha', '<', $ini)->sum('cantidad');
                $salAntes  = $allSalidas->where('distrito_id', $distrito->id)
                    ->where('tipo_especie_id', $tipo->id)->where('fecha', '<', $ini)->sum('cantidad');
                $realAntes = $allReal->where('distrito_id', $distrito->id)
                    ->where('tipo_especie_id', $tipo->id)->where('fecha', '<', $ini)->sum('cantidad');
                $nulaAntes = $allNulas->where('distrito_id', $distrito->id)
                    ->where('tipo_especie_id', $tipo->id)->where('fecha', '<', $ini)->sum('cantidad');

                $saldoInicio = $recAntes - $salAntes - $realAntes - $nulaAntes;

                $recMes  = $allDetalles->where('distrito_id', $distrito->id)
                    ->where('tipo_especie_id', $tipo->id)
                    ->whereBetween('fecha', [$ini, $fin])
                    ->sum('cantidad');
                $salMes  = $allSalidas->where('distrito_id', $distrito->id)
                    ->where('tipo_especie_id', $tipo->id)
                    ->whereBetween('fecha', [$ini, $fin])
                    ->sum('cantidad');
                $realMes = $allReal->where('distrito_id', $distrito->id)
                    ->where('tipo_especie_id', $tipo->id)
                    ->whereBetween('fecha', [$ini, $fin])
                    ->sum('cantidad');
                $montoCobrado = $allReal->where('distrito_id', $distrito->id)
                    ->where('tipo_especie_id', $tipo->id)
                    ->whereBetween('fecha', [$ini, $fin])
                    ->sum('monto_cobrado');
                $nulaMes = $allNulas->where('distrito_id', $distrito->id)
                    ->where('tipo_especie_id', $tipo->id)
                    ->whereBetween('fecha', [$ini, $fin])
                    ->sum('cantidad');

                $saldoFinal = $saldoInicio + $recMes - $salMes - $realMes - $nulaMes;

                if ($saldoInicio > 0 || $recMes > 0 || $salMes > 0 || $realMes > 0 || $nulaMes > 0) {
                    $tabla[] = [
                        'distrito'      => $distrito,
                        'tipo'          => $tipo,
                        'saldo_inicio'  => $saldoInicio,
                        'recibido'      => $recMes,
                        'salido'        => $salMes,
                        'realizado'     => $realMes,
                        'nulado'        => $nulaMes,
                        'saldo_final'   => $saldoFinal,
                        'monto_cobrado' => $montoCobrado,
                    ];
                }
            }
        }

        $html = view('frontend.admin.especies.reportes.pdf.mensual',
            compact('mes', 'anio', 'nombreMes', 'tabla'))->render();

        return PDF::loadHTML($html, $this->pdfConfig())
            ->stream("mensual-{$mes}-{$anio}.pdf");
    }

    // ─── CONSOLIDADO ANUAL POR DISTRITO ─────────────────────────────────────
    public function anual(Request $request)
    {
        $distritos = Distrito::where('activo', true)->orderBy('codigo')->get();
        $tipos     = TipoEspecie::orderBy('nombre')->get();

        if ($request->has('generar')) {
            $request->validate([
                'distrito_id'     => 'required|exists:distritos,id',
                'tipo_especie_id' => 'required|exists:tipo_especies,id',
                'anio'            => 'required|integer|min:2020',
            ]);
            return $this->pdfAnual($request);
        }

        return view('frontend.admin.especies.reportes.anual', compact('distritos', 'tipos'));
    }

    private function pdfAnual(Request $request)
    {
        $distrito = Distrito::findOrFail($request->distrito_id);
        $tipo     = TipoEspecie::findOrFail($request->tipo_especie_id);
        $anio     = (int) $request->anio;

        $inicio = Carbon::create($anio, 1, 1)->startOfYear();
        $fin    = $inicio->copy()->endOfYear();

        $real = Realizacion::where('distrito_id', $distrito->id)
            ->where('tipo_especie_id', $tipo->id)
            ->whereBetween('fecha', [$inicio, $fin])
            ->selectRaw('MONTH(fecha) as mes, denominacion_id,
                         SUM(cantidad) as cant, SUM(monto_cobrado) as monto')
            ->groupBy('mes', 'denominacion_id')->get()
            ->keyBy(fn($r) => $r->mes . '-' . $r->denominacion_id);

        $nulas = Nula::where('nulas.distrito_id', $distrito->id)
            ->whereBetween('nulas.fecha', [$inicio, $fin])
            ->join('traslado_detalles', 'nulas.traslado_detalle_id', '=', 'traslado_detalles.id')
            ->join('lotes', 'traslado_detalles.lote_id', '=', 'lotes.id')
            ->where('lotes.tipo_especie_id', $tipo->id)
            ->selectRaw('MONTH(nulas.fecha) as mes, lotes.denominacion_id,
                         SUM(nulas.numero_fin - nulas.numero_inicio + 1) as cant')
            ->groupBy('mes', 'lotes.denominacion_id')->get()
            ->keyBy(fn($r) => $r->mes . '-' . $r->denominacion_id);

        // Activas, mas las desactivadas que tuvieron movimiento en el año (si no, sus montos se pierden)
        $conMovimiento = $real->pluck('denominacion_id')->merge($nulas->pluck('denominacion_id'))->unique()->values();
        $denoms = Denominacion::where('tipo_especie_id', $tipo->id)
            ->where(fn($q) => $q->where('activo', true)->orWhereIn('id', $conMovimiento))
            ->orderBy('valor')->get();

        // Matriz mes x denominacion, con totales por fila y por columna
        $armar = function ($fuente, bool $conMonto) use ($denoms) {
            $filas = [];
            $totCol = [];
            foreach ($denoms as $d) $totCol[$d->id] = 0;
            $totGen = 0;
            $totMonto = 0;

            foreach (range(1, 12) as $m) {
                $celdas = [];
                $monto  = 0;
                foreach ($denoms as $d) {
                    $reg  = $fuente->get($m . '-' . $d->id);
                    $cant = (int) ($reg->cant ?? 0);
                    $celdas[$d->id]  = $cant;
                    $totCol[$d->id] += $cant;
                    if ($conMonto) $monto += (float) ($reg->monto ?? 0);
                }
                $filaTotal = array_sum($celdas);
                $totGen   += $filaTotal;
                $totMonto += $monto;

                $filas[] = ['mes' => $m, 'celdas' => $celdas, 'total' => $filaTotal, 'monto' => $monto];
            }

            return ['filas' => $filas, 'total_col' => $totCol, 'total' => $totGen, 'total_monto' => $totMonto];
        };

        $tablaReal  = $armar($real, true);
        $tablaNulas = $armar($nulas, false);
        $meses      = $this->meses;

        $html = view('frontend.admin.especies.reportes.pdf.anual', compact(
            'distrito', 'tipo', 'anio', 'denoms', 'tablaReal', 'tablaNulas', 'meses'
        ))->render();

        return PDF::loadHTML($html, $this->pdfConfig() + ['format' => 'A4-L'])
            ->stream("anual-{$distrito->codigo}-{$tipo->id}-{$anio}.pdf");
    }

    // ─── CONTROL DE SALDOS ──────────────────────────────────────────────────
    public function saldos(Request $request)
    {
        $distritos = Distrito::where('activo', true)->orderBy('codigo')->get();

        if ($request->has('generar')) {
            $request->validate([
                'distrito_id' => 'required|exists:distritos,id',
                'fecha_desde' => 'required|date',
                'fecha_hasta' => 'required|date|after_or_equal:fecha_desde',
            ]);
            return $this->pdfSaldos($request);
        }

        return view('frontend.admin.especies.reportes.saldos', compact('distritos'));
    }

    private function pdfSaldos(Request $request)
    {
        $distrito = Distrito::findOrFail($request->distrito_id);
        $datos    = $this->datosSaldos($distrito,
            Carbon::parse($request->fecha_desde)->startOfDay(), Carbon::parse($request->fecha_hasta)->endOfDay());

        $html = view('frontend.admin.especies.reportes.pdf.saldos', $datos)->render();

        return PDF::loadHTML($html, $this->pdfConfig())
            ->stream("saldos-{$distrito->codigo}-{$request->fecha_desde}-{$request->fecha_hasta}.pdf");
    }

    /**
     * Especies Municipales Realizadas: cada distrito con su plantilla (tipos, rotulos, costos y precios).
     * PRECIO DE VTA. solo para lo que se vende; DESCARGOS = cantidad x costo, incluidas las nulas.
     */
    public function datosSaldos(Distrito $distrito, Carbon $desde, Carbon $hasta): array
    {
        $real = Realizacion::where('distrito_id', $distrito->id)->whereBetween('fecha', [$desde, $hasta])
            ->selectRaw('denominacion_id, SUM(cantidad) as cant, SUM(monto_cobrado) as monto')
            ->groupBy('denominacion_id')->get()->keyBy('denominacion_id');

        $nulas = Nula::where('nulas.distrito_id', $distrito->id)->whereBetween('nulas.fecha', [$desde, $hasta])
            ->join('traslado_detalles', 'nulas.traslado_detalle_id', '=', 'traslado_detalles.id')
            ->join('lotes', 'traslado_detalles.lote_id', '=', 'lotes.id')
            ->selectRaw('lotes.denominacion_id, SUM(nulas.numero_fin - nulas.numero_inicio + 1) as cant')
            ->groupBy('lotes.denominacion_id')->get()->keyBy('denominacion_id');

        $config  = DistritoTipoEspecie::where('distrito_id', $distrito->id)->get()->keyBy('tipo_especie_id');
        $tarifas = DistritoDenominacion::with('denominacion')->where('distrito_id', $distrito->id)->get();

        $conMovimiento = Denominacion::whereIn('id', $real->keys()->merge($nulas->keys()))->pluck('tipo_especie_id');
        $tipos = TipoEspecie::whereIn('id', $config->keys()->merge($conMovimiento)->unique())->get()
            ->sortBy(fn($t) => [isset($config[$t->id]) ? 0 : 1, $config[$t->id]->orden ?? $t->orden, $t->id]);

        $grupos = [];
        foreach ($tipos as $tipo) {
            // Como en los libros: el tipo aparece si habia existencia al iniciar el periodo o tuvo movimiento
            $movio = $conMovimiento->contains($tipo->id) || TrasladoDetalle::whereHas('lote', fn($q) => $q->where('tipo_especie_id', $tipo->id))
                ->whereHas('traslado', fn($q) => $q->whereBetween('fecha', [$desde, $hasta])
                    ->where(fn($q2) => $q2->where('distrito_id', $distrito->id)->orWhere('origen_distrito_id', $distrito->id)))
                ->exists();
            if (!$movio && Inventario::distrito($distrito->id, $tipo->id, $desde->copy()->subDay())->sum('cantidad') == 0) {
                continue;
            }

            $plantilla = [];
            foreach ($tarifas->filter(fn($t) => $t->denominacion->tipo_especie_id == $tipo->id) as $t) {
                $costo = $t->precio_costo ?? $t->denominacion->precio_costo;
                // sin fila propia de nulas, las nulas se descargan junto con lo realizado
                $plantilla[] = ['orden' => $t->orden, 'nula' => false, 'den' => $t->denominacion_id,
                                'descripcion' => $t->descripcion ?: $t->denominacion->etiqueta, 'costo' => $costo,
                                'con_venta' => $t->precio_venta !== null, 'suma_nulas' => !$t->descripcion_nulas];
                if ($t->descripcion_nulas) {
                    $plantilla[] = ['orden' => $t->orden_nulas ?? $t->orden, 'nula' => true, 'den' => $t->denominacion_id,
                                    'descripcion' => $t->descripcion_nulas, 'costo' => $costo, 'con_venta' => $t->nulas_con_venta];
                }
            }
            // Denominaciones con movimiento pero sin tarifa del distrito: con los valores de la denominacion
            foreach (Denominacion::where('tipo_especie_id', $tipo->id)->orderBy('valor')->get() as $den) {
                if ($tarifas->contains('denominacion_id', $den->id) || (!$real->has($den->id) && !$nulas->has($den->id))) continue;
                $plantilla[] = ['orden' => 900, 'nula' => false, 'den' => $den->id, 'descripcion' => $den->etiqueta,
                                'costo' => $den->precio_costo, 'con_venta' => $den->precio_venta !== null, 'suma_nulas' => false];
                if ($nulas->has($den->id)) {
                    $plantilla[] = ['orden' => 901, 'nula' => true, 'den' => $den->id, 'descripcion' => $den->etiqueta . ' NULAS',
                                    'costo' => $den->precio_costo, 'con_venta' => false];
                }
            }
            usort($plantilla, fn($a, $b) => $a['orden'] <=> $b['orden']);

            $filas = array_map(function ($p) use ($real, $nulas) {
                $anuladas = (int) ($nulas[$p['den']]->cant ?? 0);
                $cant  = $p['nula'] ? $anuladas : (int) ($real[$p['den']]->cant ?? 0) + ($p['suma_nulas'] ? $anuladas : 0);
                $venta = !$p['con_venta'] ? null : ($p['nula'] ? 0.0 : (float) ($real[$p['den']]->monto ?? 0));
                $costo = $p['costo'] === null ? null : (float) $p['costo'];
                return ['cantidad' => $cant, 'descripcion' => $p['descripcion'], 'costo' => $costo, 'venta' => $venta,
                        'descargo' => $cant * ($costo ?? 0), 'es_nula' => $p['nula']];
            }, $plantilla);

            $ventas   = array_filter(array_column($filas, 'venta'), fn($v) => $v !== null);
            $grupos[] = [
                'tipo'           => $tipo,
                'titulo'         => $config[$tipo->id]->titulo_reporte ?? mb_strtoupper($tipo->nombre),
                'filas'          => $filas,
                'total_venta'    => $ventas ? array_sum($ventas) : null,
                'total_descargo' => array_sum(array_column($filas, 'descargo')),
            ];
        }

        $mesCompleto = $desde->day === 1 && $hasta->isSameDay($desde->copy()->endOfMonth());

        return [
            'distrito'             => $distrito,
            'desde'                => $desde,
            'hasta'                => $hasta,
            'periodo'              => $mesCompleto
                ? mb_strtoupper($this->meses[$desde->month]) . ' ' . $desde->year
                : 'DEL ' . $desde->format('d/m/Y') . ' AL ' . $hasta->format('d/m/Y'),
            'grupos'               => $grupos,
            'totalVtaGeneral'      => array_sum(array_map(fn($g) => $g['total_venta'] ?? 0, $grupos)),
            'totalDescargoGeneral' => array_sum(array_column($grupos, 'total_descargo')),
            'faltaCosto'           => collect($grupos)->pluck('filas')->collapse()->contains(fn($f) => $f['costo'] === null),
            'firma'                => config('especies.firmas.tesorera'),
        ];
    }
}
