<?php

namespace App\Http\Controllers;

//controlador del reporte de consultas: muestra formulario, genera PDF y lo sirve desde cache
use App\Models\Consulta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;

class ConsultaController extends Controller
{
    //muestra el formulario de seleccion de periodo y el iframe del PDF si hay token en sesion
    public function index()
    {
        $token       = session('reporte_token');
        $fechaInicio = session('reporte_fecha_inicio');
        $fechaFin    = session('reporte_fecha_fin');

        return view('frontend.admin.consultas.index', compact('token', 'fechaInicio', 'fechaFin'));
    }

    //valida el periodo, agrupa las consultas por NIT/DUI, genera el PDF y redirige con token
    public function generarPdf(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date|after_or_equal:fecha_inicio',
        ], [
            'fecha_inicio.required'    => 'Debe seleccionar la fecha de inicio.',
            'fecha_inicio.date'        => 'La fecha de inicio no es válida.',
            'fecha_fin.required'       => 'Debe seleccionar la fecha de finalización.',
            'fecha_fin.date'           => 'La fecha de finalización no es válida.',
            'fecha_fin.after_or_equal' => 'La fecha de finalización debe ser igual o posterior a la de inicio.',
        ]);

        $fechaInicio = $request->fecha_inicio;
        $fechaFin    = $request->fecha_fin;

        //agrupa por NIT/DUI y cuenta cuantas veces se consulto dentro del periodo
        $datos = Consulta::whereBetween('buscado_en', [
                $fechaInicio . ' 00:00:00',
                $fechaFin    . ' 23:59:59',
            ])
            ->select('nitdui', DB::raw('MAX(nombre) as nombre'), DB::raw('COUNT(*) as total_consultas'))
            ->groupBy('nitdui')
            ->orderBy('nitdui')
            ->get();

        if ($datos->isEmpty()) {
            return back()->withInput()
                ->with('buscar_error', 'No se encontraron consultas para el periodo seleccionado.');
        }

        $html = view()->file(public_path('reportes/Reporte_consultas.blade.php'), [
            'datos'       => $datos,
            'fechaInicio' => $fechaInicio,
            'fechaFin'    => $fechaFin,
            'generadoEn'  => now()->format('d/m/Y H:i:s'),
        ])->render();

        $config = [
            'margin_top'    => 15,
            'margin_bottom' => 15,
            'margin_left'   => 20,
            'margin_right'  => 20,
        ];

        try {
            $pdfStr = PDF::loadHTML($html, $config)->output();
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('buscar_error', 'Error al generar el PDF. Intente de nuevo.');
        }

        $token = (string) Str::uuid();
        cache()->put('reporte_consultas_pdf_' . $token, base64_encode($pdfStr), now()->addMinutes(30));

        return redirect()->route('admin.consultas.index')->with([
            'reporte_token'        => $token,
            'reporte_fecha_inicio' => $fechaInicio,
            'reporte_fecha_fin'    => $fechaFin,
        ]);
    }

    //sirve el PDF del reporte almacenado en cache para visualizacion inline en el iframe
    public function verPdf(string $token)
    {
        $encoded = cache()->get('reporte_consultas_pdf_' . $token);
        if (!$encoded) {
            abort(404);
        }
        return response(base64_decode($encoded), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="reporte_consultas.pdf"',
        ]);
    }
}
