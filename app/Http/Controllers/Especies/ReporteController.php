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

    private function pdfLibro(Request $request)
    {
        $distrito = Distrito::findOrFail($request->distrito_id);
        $tipo     = TipoEspecie::findOrFail($request->tipo_especie_id);
        $mes      = (int) $request->mes;
        $anio     = (int) $request->anio;
        $denomFiltro = $request->denominacion_id ? Denominacion::find($request->denominacion_id) : null;

        $inicioMes = Carbon::create($anio, $mes, 1)->startOfMonth();
        $finMes    = $inicioMes->copy()->endOfMonth();

        // Saldo inicial: recibido antes del mes - realizado antes - nulado antes
        $recibidoAntes = TrasladoDetalle::whereHas('traslado',
                fn($q) => $q->where('distrito_id', $distrito->id)->where('fecha', '<', $inicioMes))
            ->whereHas('lote', fn($q) => $q->where('tipo_especie_id', $tipo->id))
            ->sum('cantidad');

        $realizadoAntes = Realizacion::where('distrito_id', $distrito->id)
            ->where('tipo_especie_id', $tipo->id)->where('fecha', '<', $inicioMes)->sum('cantidad');

        $nuladoAntes = Nula::whereHas('trasladoDetalle',
                fn($q) => $q->whereHas('traslado', fn($q2) => $q2->where('distrito_id', $distrito->id))
                            ->whereHas('lote',    fn($q2) => $q2->where('tipo_especie_id', $tipo->id)))
            ->where('fecha', '<', $inicioMes)
            ->selectRaw('COALESCE(SUM(numero_fin - numero_inicio + 1), 0) as t')->value('t') ?? 0;

        $saldoInicio = $recibidoAntes - $realizadoAntes - $nuladoAntes;

        // Traslados recibidos en el mes
        $trasladosMes = TrasladoDetalle::whereHas('traslado',
                fn($q) => $q->where('distrito_id', $distrito->id)->whereBetween('fecha', [$inicioMes, $finMes]))
            ->whereHas('lote', fn($q) => $q->where('tipo_especie_id', $tipo->id))
            ->with(['traslado', 'lote.compra'])
            ->orderBy('numero_inicio')->get();

        // Realizaciones del mes
        $realizacionesMes = Realizacion::where('distrito_id', $distrito->id)
            ->where('tipo_especie_id', $tipo->id)
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->when($denomFiltro, fn($q) => $q->where('denominacion_id', $denomFiltro->id))
            ->with('denominacion')->orderBy('fecha')->orderBy('numero_inicio')->get();

        // Nulas del mes
        $nulasMes = Nula::whereHas('trasladoDetalle',
                fn($q) => $q->whereHas('traslado', fn($q2) => $q2->where('distrito_id', $distrito->id))
                            ->whereHas('lote',    fn($q2) => $q2->where('tipo_especie_id', $tipo->id)))
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->orderBy('fecha')->orderBy('numero_inicio')->get();

        $nombreMes = $this->meses[$mes];

        $html = view('frontend.admin.especies.reportes.pdf.libro', compact(
            'distrito', 'tipo', 'mes', 'anio', 'nombreMes', 'denomFiltro',
            'saldoInicio', 'trasladosMes', 'realizacionesMes', 'nulasMes'
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
                'fecha_corte'     => 'required|date',
            ]);
            return $this->pdfBodega($request);
        }

        return view('frontend.admin.especies.reportes.bodega', compact('tipos'));
    }

    private function pdfBodega(Request $request)
    {
        $tipo       = TipoEspecie::findOrFail($request->tipo_especie_id);
        $fechaCorte = Carbon::parse($request->fecha_corte)->endOfDay();

        $rangos = LoteRango::whereHas('lote',
                fn($q) => $q->where('tipo_especie_id', $tipo->id)
                            ->whereHas('compra', fn($q2) => $q2->where('fecha', '<=', $fechaCorte)))
            ->with(['lote.compra'])
            ->orderBy('lote_id')->orderBy('numero_inicio')->get();

        // Cuánto fue trasladado de cada lote hasta la fecha corte
        $trasladado = TrasladoDetalle::whereHas('lote', fn($q) => $q->where('tipo_especie_id', $tipo->id))
            ->whereHas('traslado', fn($q) => $q->where('fecha', '<=', $fechaCorte))
            ->selectRaw('lote_id, SUM(cantidad) as total')
            ->groupBy('lote_id')->pluck('total', 'lote_id');

        $lotes = $rangos->groupBy('lote_id')->map(function ($rs) use ($trasladado) {
            $lote        = $rs->first()->lote;
            $totalRangos = $rs->sum(fn($r) => $r->numero_fin - $r->numero_inicio + 1);
            $disponible  = max(0, $totalRangos - $trasladado->get($lote->id, 0));
            return compact('lote', 'disponible') + ['rangos' => $rs, 'total' => $totalRangos];
        })->filter(fn($l) => $l['disponible'] > 0);

        $html = view('frontend.admin.especies.reportes.pdf.bodega',
            compact('tipo', 'fechaCorte', 'lotes'))->render();

        return PDF::loadHTML($html, $this->pdfConfig())
            ->stream("bodega-{$tipo->id}-{$request->fecha_corte}.pdf");
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
            ->with(['lote.compra', 'traslado'])
            ->orderBy('numero_inicio')->get();

        $nulasPorDetalle = Nula::whereIn('traslado_detalle_id', $detalles->pluck('id'))
            ->where('fecha', '<=', $fechaCorte)
            ->selectRaw('traslado_detalle_id, SUM(numero_fin - numero_inicio + 1) as t')
            ->groupBy('traslado_detalle_id')->pluck('t', 'traslado_detalle_id');

        $realizadoTotal = Realizacion::where('distrito_id', $distrito->id)
            ->where('tipo_especie_id', $tipo->id)->where('fecha', '<=', $fechaCorte)->sum('cantidad');

        $rows = $detalles->map(fn($d) => [
            'detalle'    => $d,
            'anulado'    => $nulasPorDetalle->get($d->id, 0),
            'disponible' => max(0, $d->cantidad - $nulasPorDetalle->get($d->id, 0)),
        ]);

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
        $allDetalles = TrasladoDetalle::join('traslados', 'traslado_detalles.traslado_id', '=', 'traslados.id')
            ->join('lotes', 'traslado_detalles.lote_id', '=', 'lotes.id')
            ->where('traslados.fecha', '<=', $finMes)
            ->select('traslados.distrito_id', 'lotes.tipo_especie_id',
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
                $realAntes = $allReal->where('distrito_id', $distrito->id)
                    ->where('tipo_especie_id', $tipo->id)->where('fecha', '<', $inicioMes)->sum('cantidad');
                $nulaAntes = $allNulas->where('distrito_id', $distrito->id)
                    ->where('tipo_especie_id', $tipo->id)->where('fecha', '<', $inicioMes)->sum('cantidad');

                $saldoInicio = $recAntes - $realAntes - $nulaAntes;

                $recMes  = $allDetalles->where('distrito_id', $distrito->id)
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

                $saldoFinal = $saldoInicio + $recMes - $realMes - $nulaMes;

                if ($saldoInicio > 0 || $recMes > 0 || $realMes > 0 || $nulaMes > 0) {
                    $tabla[] = [
                        'distrito'      => $distrito,
                        'tipo'          => $tipo,
                        'saldo_inicio'  => $saldoInicio,
                        'recibido'      => $recMes,
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
}
