<?php

namespace App\Http\Controllers\Especies;

use App\Http\Controllers\Controller;
use App\Models\Denominacion;
use App\Models\Distrito;
use App\Models\LoteRango;
use App\Models\Nula;
use App\Models\Realizacion;
use App\Models\TipoEspecie;
use App\Models\TrasladoDetalle;
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
        $tipos     = TipoEspecie::where('activo', true)->orderBy('nombre')->get();

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

    /**
     * Resta de $base los tramos de $quitar y devuelve los intervalos que quedan.
     * Ambos son arreglos de pares [inicio, fin].
     */
    private function restarIntervalos(array $base, array $quitar): array
    {
        foreach ($quitar as [$qi, $qf]) {
            $resto = [];
            foreach ($base as [$bi, $bf]) {
                if ($qf < $bi || $qi > $bf) { $resto[] = [$bi, $bf]; continue; }
                if ($qi > $bi) $resto[] = [$bi, min($bf, $qi - 1)];
                if ($qf < $bf) $resto[] = [max($bi, $qf + 1), $bf];
            }
            $base = $resto;
        }
        return $base;
    }

    /**
     * Existencia real de un distrito a una fecha, lote por lote y con los rangos
     * que siguen vivos: recibido menos realizado, nulado y salidas.
     */
    private function existenciaPorLote(int $distritoId, int $tipoId, Carbon $hasta)
    {
        $entradas = TrasladoDetalle::whereHas('traslado',
                fn($q) => $q->where('distrito_id', $distritoId)->where('fecha', '<=', $hasta))
            ->whereHas('lote', fn($q) => $q->where('tipo_especie_id', $tipoId))
            ->with('lote.denominacion', 'lote.compra')
            ->get();

        $salidas = TrasladoDetalle::whereHas('traslado', fn($q) =>
                $q->whereIn('tipo', ['distrito_bodega', 'distrito_distrito'])
                  ->where('origen_distrito_id', $distritoId)
                  ->where('fecha', '<=', $hasta))
            ->whereHas('lote', fn($q) => $q->where('tipo_especie_id', $tipoId))
            ->get(['lote_id', 'numero_inicio', 'numero_fin']);

        $reales = Realizacion::where('distrito_id', $distritoId)
            ->where('tipo_especie_id', $tipoId)
            ->where('fecha', '<=', $hasta)
            ->get(['numero_inicio', 'numero_fin']);

        $nulas = Nula::where('nulas.distrito_id', $distritoId)
            ->where('nulas.fecha', '<=', $hasta)
            ->join('traslado_detalles', 'nulas.traslado_detalle_id', '=', 'traslado_detalles.id')
            ->join('lotes', 'traslado_detalles.lote_id', '=', 'lotes.id')
            ->where('lotes.tipo_especie_id', $tipoId)
            ->get(['nulas.numero_inicio', 'nulas.numero_fin']);

        // Los correlativos son unicos por tipo, asi que un rango consumido solo
        // puede solapar con el lote que realmente lo contiene.
        // concat y no merge: merge deduplica por llave primaria y estas
        // consultas no seleccionan el id, asi que colapsarian a un solo registro
        $consumidoGlobal = $reales->concat($nulas)
            ->map(fn($r) => [$r->numero_inicio, $r->numero_fin])->values()->all();

        return $entradas->groupBy('lote_id')->map(function ($ds) use ($salidas, $consumidoGlobal) {
            $lote = $ds->first()->lote;

            $base   = $ds->map(fn($d) => [$d->numero_inicio, $d->numero_fin])->values()->all();
            $quitar = array_merge(
                $salidas->where('lote_id', $lote->id)
                    ->map(fn($s) => [$s->numero_inicio, $s->numero_fin])->values()->all(),
                $consumidoGlobal
            );

            $intervalos = $this->restarIntervalos($base, $quitar);
            usort($intervalos, fn($a, $b) => $a[0] <=> $b[0]);

            $cantidad = array_sum(array_map(fn($i) => $i[1] - $i[0] + 1, $intervalos));
            $valor    = (float) ($lote->denominacion->valor ?? 0);

            return [
                'lote'       => $lote,
                'intervalos' => $intervalos,
                'cantidad'   => $cantidad,
                'valor'      => $valor,
                'monto'      => $cantidad * $valor,
            ];
        })->filter(fn($r) => $r['cantidad'] > 0)
          ->sortBy(fn($r) => $r['valor'])
          ->values();
    }

    private function pdfLibro(Request $request)
    {
        $distrito = Distrito::findOrFail($request->distrito_id);
        $tipo     = TipoEspecie::findOrFail($request->tipo_especie_id);
        $mes      = (int) $request->mes;
        $anio     = (int) $request->anio;
        $denomFiltro = $request->denominacion_id ? Denominacion::find($request->denominacion_id) : null;

        $inicioMes = Carbon::create($anio, $mes, 1)->startOfMonth();
        $finMes    = $inicioMes->copy()->endOfMonth();

        // Saldo anterior y saldo a nueva cuenta, con rangos y valuados
        $saldoInicioDet = $this->existenciaPorLote($distrito->id, $tipo->id, $inicioMes->copy()->subDay());
        $saldoFinalDet  = $this->existenciaPorLote($distrito->id, $tipo->id, $finMes);

        $saldoInicio      = $saldoInicioDet->sum('cantidad');
        $saldoInicioMonto = $saldoInicioDet->sum('monto');
        $saldoFinal       = $saldoFinalDet->sum('cantidad');
        $saldoFinalMonto  = $saldoFinalDet->sum('monto');

        // Traslados recibidos en el mes
        $trasladosMes = TrasladoDetalle::whereHas('traslado',
                fn($q) => $q->where('distrito_id', $distrito->id)->whereBetween('fecha', [$inicioMes, $finMes]))
            ->whereHas('lote', fn($q) => $q->where('tipo_especie_id', $tipo->id))
            ->with(['traslado', 'lote.compra', 'lote.denominacion'])
            ->orderBy('numero_inicio')->get();

        // Salidas del mes: devoluciones a bodega o envios a otro distrito
        $salidasMes = TrasladoDetalle::whereHas('traslado', fn($q) =>
                $q->whereIn('tipo', ['distrito_bodega', 'distrito_distrito'])
                  ->where('origen_distrito_id', $distrito->id)
                  ->whereBetween('fecha', [$inicioMes, $finMes]))
            ->whereHas('lote', fn($q) => $q->where('tipo_especie_id', $tipo->id))
            ->with(['traslado.distrito', 'lote.compra', 'lote.denominacion'])
            ->orderBy('numero_inicio')->get();

        // Realizaciones del mes
        $realizacionesMes = Realizacion::where('distrito_id', $distrito->id)
            ->where('tipo_especie_id', $tipo->id)
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->when($denomFiltro, fn($q) => $q->where('denominacion_id', $denomFiltro->id))
            ->with('denominacion')->orderBy('fecha')->orderBy('numero_inicio')->get();

        // Nulas del mes, con la denominacion del lote para poder valuarlas
        $nulasMes = Nula::whereHas('trasladoDetalle',
                fn($q) => $q->whereHas('traslado', fn($q2) => $q2->where('distrito_id', $distrito->id))
                            ->whereHas('lote',    fn($q2) => $q2->where('tipo_especie_id', $tipo->id)))
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->with('trasladoDetalle.lote.denominacion')
            ->orderBy('fecha')->orderBy('numero_inicio')->get();

        $nombreMes = $this->meses[$mes];

        $html = view('frontend.admin.especies.reportes.pdf.libro', compact(
            'distrito', 'tipo', 'mes', 'anio', 'nombreMes', 'denomFiltro',
            'saldoInicioDet', 'saldoInicio', 'saldoInicioMonto',
            'saldoFinalDet', 'saldoFinal', 'saldoFinalMonto',
            'trasladosMes', 'salidasMes', 'realizacionesMes', 'nulasMes'
        ))->render();

        return PDF::loadHTML($html, $this->pdfConfig())
            ->stream("libro-{$distrito->codigo}-{$mes}-{$anio}.pdf");
    }

    // ─── EXISTENCIAS EN BODEGA ───────────────────────────────────────────────
    public function bodega(Request $request)
    {
        $tipos = TipoEspecie::where('activo', true)->orderBy('nombre')->get();

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
        $tipos     = TipoEspecie::where('activo', true)->orderBy('nombre')->get();

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

        $detalles = TrasladoDetalle::whereHas('traslado',
                fn($q) => $q->where('distrito_id', $distrito->id)->where('fecha', '<=', $fechaCorte))
            ->whereHas('lote', fn($q) => $q->where('tipo_especie_id', $tipo->id))
            ->with(['lote.compra', 'lote.denominacion', 'traslado'])
            ->orderBy('numero_inicio')->get();

        $nulasPorDetalle = Nula::whereIn('traslado_detalle_id', $detalles->pluck('id'))
            ->where('fecha', '<=', $fechaCorte)
            ->selectRaw('traslado_detalle_id, SUM(numero_fin - numero_inicio + 1) as t')
            ->groupBy('traslado_detalle_id')->pluck('t', 'traslado_detalle_id');

        // Realizaciones y salidas al corte, para descontarlas del rango de cada detalle
        $realizaciones = Realizacion::where('distrito_id', $distrito->id)
            ->where('tipo_especie_id', $tipo->id)
            ->where('fecha', '<=', $fechaCorte)
            ->get(['numero_inicio', 'numero_fin']);

        $salidas = TrasladoDetalle::whereHas('traslado', fn($q) =>
                $q->whereIn('tipo', ['distrito_bodega', 'distrito_distrito'])
                  ->where('origen_distrito_id', $distrito->id)
                  ->where('fecha', '<=', $fechaCorte))
            ->whereHas('lote', fn($q) => $q->where('tipo_especie_id', $tipo->id))
            ->get(['lote_id', 'numero_inicio', 'numero_fin']);

        $realizadoTotal = Realizacion::where('distrito_id', $distrito->id)
            ->where('tipo_especie_id', $tipo->id)->where('fecha', '<=', $fechaCorte)->sum('cantidad');

        // Documentos de [a,b] cubiertos por los rangos de $conjunto
        $solape = fn($conjunto, $a, $b) => $conjunto->sum(
            fn($r) => max(0, min($b, $r->numero_fin) - max($a, $r->numero_inicio) + 1)
        );

        $rows = $detalles->map(function ($d) use ($nulasPorDetalle, $realizaciones, $salidas, $solape) {
            $anulado   = (int) $nulasPorDetalle->get($d->id, 0);
            $realizado = $solape($realizaciones, $d->numero_inicio, $d->numero_fin);
            $salido    = $solape($salidas->where('lote_id', $d->lote_id), $d->numero_inicio, $d->numero_fin);
            $valor     = (float) ($d->lote->denominacion->valor ?? 0);
            $disp      = max(0, $d->cantidad - $anulado - $realizado - $salido);

            return [
                'detalle'    => $d,
                'anulado'    => $anulado,
                'realizado'  => $realizado,
                'salido'     => $salido,
                'disponible' => $disp,
                'valor'      => $valor,
                'saldo'      => $disp * $valor,
            ];
        });

        $html = view('frontend.admin.especies.reportes.pdf.distritos',
            compact('distrito', 'tipo', 'fechaCorte', 'rows', 'realizadoTotal'))->render();

        return PDF::loadHTML($html, $this->pdfConfig())
            ->stream("distrito-{$distrito->codigo}-{$request->fecha_corte}.pdf");
    }

    // ─── REALIZACIONES POR PERIODO ───────────────────────────────────────────
    public function realizaciones(Request $request)
    {
        $distritos = Distrito::where('activo', true)->orderBy('codigo')->get();
        $tipos     = TipoEspecie::where('activo', true)->orderBy('nombre')->get();

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
        $tipos     = TipoEspecie::where('activo', true)->orderBy('nombre')->get();

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

        $detalles = TrasladoDetalle::whereHas('traslado',
                fn($q) => $q->where('distrito_id', $distrito->id)->whereBetween('fecha', [$desde, $hasta]))
            ->whereHas('lote', fn($q) => $q->where('tipo_especie_id', $tipo->id))
            ->with(['traslado', 'lote.compra'])
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

        $distritos = Distrito::where('activo', true)->orderBy('codigo')->get();
        $tipos     = TipoEspecie::where('activo', true)->orderBy('nombre')->get();

        // Cargar todos los datos en bulk para eficiencia
        // Solo traslados entrantes a distritos (bodega_distrito y distrito_distrito como destino)
        $allDetalles = TrasladoDetalle::join('traslados', 'traslado_detalles.traslado_id', '=', 'traslados.id')
            ->join('lotes', 'traslado_detalles.lote_id', '=', 'lotes.id')
            ->where('traslados.fecha', '<=', $finMes)
            ->whereNotNull('traslados.distrito_id')
            ->select('traslados.distrito_id', 'lotes.tipo_especie_id',
                     'traslados.fecha', 'traslado_detalles.cantidad')
            ->get();

        // Salidas desde distritos (devoluciones y traslados inter-distrito como origen)
        $allSalidas = TrasladoDetalle::join('traslados', 'traslado_detalles.traslado_id', '=', 'traslados.id')
            ->join('lotes', 'traslado_detalles.lote_id', '=', 'lotes.id')
            ->where('traslados.fecha', '<=', $finMes)
            ->whereIn('traslados.tipo', ['distrito_bodega', 'distrito_distrito'])
            ->whereNotNull('traslados.origen_distrito_id')
            ->select('traslados.origen_distrito_id as distrito_id', 'lotes.tipo_especie_id',
                     'traslados.fecha', 'traslado_detalles.cantidad')
            ->get();

        $allReal = Realizacion::where('fecha', '<=', $finMes)
            ->select('distrito_id', 'tipo_especie_id', 'fecha', 'cantidad', 'monto_cobrado')->get();

        $allNulas = Nula::join('traslado_detalles', 'nulas.traslado_detalle_id', '=', 'traslado_detalles.id')
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
                    ->where('tipo_especie_id', $tipo->id)->where('fecha', '<', $inicioMes)->sum('cantidad');
                $salAntes  = $allSalidas->where('distrito_id', $distrito->id)
                    ->where('tipo_especie_id', $tipo->id)->where('fecha', '<', $inicioMes)->sum('cantidad');
                $realAntes = $allReal->where('distrito_id', $distrito->id)
                    ->where('tipo_especie_id', $tipo->id)->where('fecha', '<', $inicioMes)->sum('cantidad');
                $nulaAntes = $allNulas->where('distrito_id', $distrito->id)
                    ->where('tipo_especie_id', $tipo->id)->where('fecha', '<', $inicioMes)->sum('cantidad');

                $saldoInicio = $recAntes - $salAntes - $realAntes - $nulaAntes;

                $recMes  = $allDetalles->where('distrito_id', $distrito->id)
                    ->where('tipo_especie_id', $tipo->id)
                    ->whereBetween('fecha', [$inicioMes->toDateString(), $finMes->toDateString()])
                    ->sum('cantidad');
                $salMes  = $allSalidas->where('distrito_id', $distrito->id)
                    ->where('tipo_especie_id', $tipo->id)
                    ->whereBetween('fecha', [$inicioMes->toDateString(), $finMes->toDateString()])
                    ->sum('cantidad');
                $realMes = $allReal->where('distrito_id', $distrito->id)
                    ->where('tipo_especie_id', $tipo->id)
                    ->whereBetween('fecha', [$inicioMes->toDateString(), $finMes->toDateString()])
                    ->sum('cantidad');
                $montoCobrado = $allReal->where('distrito_id', $distrito->id)
                    ->where('tipo_especie_id', $tipo->id)
                    ->whereBetween('fecha', [$inicioMes->toDateString(), $finMes->toDateString()])
                    ->sum('monto_cobrado');
                $nulaMes = $allNulas->where('distrito_id', $distrito->id)
                    ->where('tipo_especie_id', $tipo->id)
                    ->whereBetween('fecha', [$inicioMes->toDateString(), $finMes->toDateString()])
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
        $tipos     = TipoEspecie::where('activo', true)->orderBy('nombre')->get();

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
        $tipo     = TipoEspecie::with(['denominaciones' => fn($q) => $q->where('activo', true)->orderBy('valor')])
            ->findOrFail($request->tipo_especie_id);
        $anio = (int) $request->anio;

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

        $denoms = $tipo->denominaciones;

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
        $desde    = Carbon::parse($request->fecha_desde)->startOfDay();
        $hasta    = Carbon::parse($request->fecha_hasta)->endOfDay();

        // Realizado del periodo por tipo + denominacion
        $realPorDenom = Realizacion::where('distrito_id', $distrito->id)
            ->whereBetween('fecha', [$desde, $hasta])
            ->selectRaw('tipo_especie_id, denominacion_id, SUM(cantidad) as cant')
            ->groupBy('tipo_especie_id', 'denominacion_id')
            ->get()
            ->keyBy(fn($r) => $r->tipo_especie_id . '-' . $r->denominacion_id);

        // Nulado del periodo: la denominacion viene del lote del detalle de traslado
        $nulasPorDenom = Nula::where('nulas.distrito_id', $distrito->id)
            ->whereBetween('nulas.fecha', [$desde, $hasta])
            ->join('traslado_detalles', 'nulas.traslado_detalle_id', '=', 'traslado_detalles.id')
            ->join('lotes', 'traslado_detalles.lote_id', '=', 'lotes.id')
            ->selectRaw('lotes.tipo_especie_id, lotes.denominacion_id,
                         SUM(nulas.numero_fin - nulas.numero_inicio + 1) as cant')
            ->groupBy('lotes.tipo_especie_id', 'lotes.denominacion_id')
            ->get()
            ->keyBy(fn($r) => $r->tipo_especie_id . '-' . $r->denominacion_id);

        // Tipos que este distrito maneja: los que alguna vez recibio, mas los que tuvieron movimiento
        $tipoIds = TrasladoDetalle::whereHas('traslado', fn($q) => $q->where('distrito_id', $distrito->id))
            ->join('lotes', 'traslado_detalles.lote_id', '=', 'lotes.id')
            ->distinct()->pluck('lotes.tipo_especie_id')
            ->merge($realPorDenom->pluck('tipo_especie_id'))
            ->merge($nulasPorDenom->pluck('tipo_especie_id'))
            ->unique();

        $tipos = TipoEspecie::whereIn('id', $tipoIds)
            ->with(['denominaciones' => fn($q) => $q->where('activo', true)->orderBy('valor')])
            ->orderBy('nombre')->get();

        $faltaCosto = false;

        $grupos = $tipos->map(function ($tipo) use ($realPorDenom, $nulasPorDenom, &$faltaCosto) {
            $filas         = [];
            $totalCantidad = 0;
            $totalVta      = 0;
            $totalDescargo = 0;

            // Una fila por denominacion activa, aunque el periodo no tenga movimiento
            foreach ($tipo->denominaciones as $den) {
                $cant = (int) ($realPorDenom->get($tipo->id . '-' . $den->id)->cant ?? 0);
                $vta  = $cant * $den->valor;
                $desc = $cant * (float) ($den->precio_costo ?? 0);

                if ($den->precio_costo === null) $faltaCosto = true;

                $filas[] = [
                    'etiqueta'   => $den->etiqueta,
                    'cantidad'   => $cant,
                    'costo'      => $den->precio_costo,
                    'precio_vta' => $vta,
                    'descargo'   => $desc,
                    'es_nula'    => false,
                ];

                $totalCantidad += $cant;
                $totalVta      += $vta;
                $totalDescargo += $desc;
            }

            // Las nulas van despues, solo cuando existen. Descargan inventario pero no generan venta
            foreach ($tipo->denominaciones as $den) {
                $cant = (int) ($nulasPorDenom->get($tipo->id . '-' . $den->id)->cant ?? 0);
                if ($cant === 0) continue;

                $desc = $cant * (float) ($den->precio_costo ?? 0);

                $filas[] = [
                    'etiqueta'   => $den->etiqueta . ' NULAS',
                    'cantidad'   => $cant,
                    'costo'      => $den->precio_costo,
                    'precio_vta' => $cant * $den->valor,
                    'descargo'   => $desc,
                    'es_nula'    => true,
                ];

                $totalDescargo += $desc;
            }

            return [
                'tipo'           => $tipo,
                'filas'          => $filas,
                'total_cantidad' => $totalCantidad,
                'total_vta'      => $totalVta,
                'total_descargo' => $totalDescargo,
            ];
        })->values();

        $totalVtaGeneral      = $grupos->sum('total_vta');
        $totalDescargoGeneral = $grupos->sum('total_descargo');
        $totalCantidad        = $grupos->sum('total_cantidad');

        $html = view('frontend.admin.especies.reportes.pdf.saldos', compact(
            'distrito', 'desde', 'hasta', 'grupos',
            'totalVtaGeneral', 'totalDescargoGeneral', 'totalCantidad', 'faltaCosto'
        ))->render();

        return PDF::loadHTML($html, $this->pdfConfig())
            ->stream("saldos-{$distrito->codigo}-{$request->fecha_desde}-{$request->fecha_hasta}.pdf");
    }
}
