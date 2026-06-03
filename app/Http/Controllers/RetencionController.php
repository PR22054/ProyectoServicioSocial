<?php

namespace App\Http\Controllers;

use App\Models\Anio;
use App\Models\Consulta;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;
use PhpOffice\PhpSpreadsheet\IOFactory;

//controlador compartido entre admin y empleado para buscar y generar constancias de retencion
class RetencionController extends Controller
{
    //carga los anos disponibles, determina el layout y recupera resultados de sesion si existen
    public function index()
    {
        $anios   = Anio::orderBy('anio', 'desc')->get();
        //si hay sesion de admin se usa el layout completo, de lo contrario el layout publico de empleado
        $layout  = (auth()->check() && auth()->user()->hasRole('admin'))
            ? 'frontend.layouts.admin'
            : 'frontend.layouts.empleado';

        $token   = session('pdf_token');
        $count   = session('pdf_count');
        $anioSel = session('pdf_anio_sel');
        $nitSel  = session('pdf_nit_sel');

        return view('frontend.retencion.index', compact('anios', 'layout', 'token', 'count', 'anioSel', 'nitSel'));
    }

    //busca el NIT/DUI en el excel del ano seleccionado y genera las constancias en PDF
    public function buscar(Request $request)
    {
        //si ambos campos estan vacios se muestra un mensaje que referencia los dos
        if (!$request->filled('anio_id') && !$request->filled('nitdui')) {
            return back()->withInput()
                ->withErrors(['anio_id' => 'Debe seleccionar un año e ingresar el NIT/DUI para buscar.']);
        }

        $request->validate([
            'anio_id' => 'required|exists:anios,id',
            'nitdui'  => 'required|string',
        ], [
            'anio_id.required' => 'Debe seleccionar un año.',
            'anio_id.exists'   => 'El año seleccionado no es válido.',
            'nitdui.required'  => 'Debe ingresar el NIT/DUI.',
        ]);

        $anio = Anio::find($request->anio_id);

        if (!$anio->archivo_excel) {
            return back()->withInput()
                ->with('buscar_error', 'El año seleccionado no tiene un archivo Excel asociado.');
        }

        $path = public_path('excel/' . $anio->archivo_excel);

        if (!file_exists($path)) {
            return back()->withInput()
                ->with('buscar_error', 'No se encontró el archivo Excel del año seleccionado.');
        }

        $nitBuscado = trim($request->nitdui);

        try {
            $spreadsheet = IOFactory::load($path);
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('buscar_error', 'No se pudo leer el archivo Excel. Verifique que el archivo sea válido.');
        }

        $meses = ['ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO',
                  'AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE'];

        $fechaHoy = [
            'dia'          => now()->day,
            'mes'          => $meses[now()->month - 1],
            'anio_emision' => now()->year,
        ];

        $html1 = null;
        $html2 = null;
        $nombreConsulta = null;

        //buscar en hoja COD 01-60-80-81 (empleados con esos codigos de retencion)
        $sheet1 = $spreadsheet->getSheetByName('COD 01-60-80-81');
        if ($sheet1) {
            $fila = $this->buscarNit($sheet1, $nitBuscado);
            if ($fila) {
                $nombreConsulta = $fila['B'];
                $data = array_merge($fechaHoy, [
                    'nombre'            => $fila['B'],
                    'nit'               => $fila['C'],
                    'codigo'            => $fila['D'],
                    'anio'              => $anio->anio,
                    'monto_devengado'   => $this->numVal($fila['E']),
                    'impuesto_retenido' => $this->numVal($fila['F']),
                    'aguinaldo_exento'  => $this->numVal($fila['G']),
                    'aguinaldo_gravado' => $this->numVal($fila['H']),
                    'isss'              => $this->numVal($fila['I']),
                    'afp'               => $this->numVal($fila['J']),
                    'ipsfa'             => $this->numVal($fila['K']),
                    'inpep'             => $this->numVal($fila['L']),
                ]);
                $html1 = view()->file(public_path('constancias/Cod_01-60-80-81.blade.php'), $data)->render();
            }
        }

        //buscar en hoja COD 11-27-70-84 (empleados con esos codigos de retencion)
        $sheet2 = $spreadsheet->getSheetByName('COD 11-27-70-84');
        if ($sheet2) {
            $fila = $this->buscarNit($sheet2, $nitBuscado);
            if ($fila) {
                if (!$nombreConsulta) $nombreConsulta = $fila['B'];
                $data = array_merge($fechaHoy, [
                    'nombre'            => $fila['B'],
                    'nit'               => $fila['C'],
                    'codigo'            => $fila['D'],
                    'anio'              => $anio->anio,
                    'monto_devengado'   => $this->numVal($fila['E']),
                    'impuesto_retenido' => $this->numVal($fila['F']),
                ]);
                $html2 = view()->file(public_path('constancias/Cod_11-27-70-84.blade.php'), $data)->render();
            }
        }

        //registrar la busqueda en la tabla consultas independientemente del resultado
        Consulta::create([
            'nitdui'     => $nitBuscado,
            'nombre'     => $nombreConsulta,
            'buscado_en' => now(),
        ]);

        $encontrados = ($html1 ? 1 : 0) + ($html2 ? 1 : 0);

        if ($encontrados === 0) {
            return back()->withInput()
                ->with('buscar_error', 'No se encontró ningún registro con el NIT/DUI ingresado.');
        }

        $config = [
            'margin_top'    => 15,
            'margin_bottom' => 15,
            'margin_left'   => 20,
            'margin_right'  => 20,
        ];

        try {
            //si hay dos constancias se combinan en un unico PDF con salto de pagina entre ellas
            if ($html1 && $html2) {
                $estilo  = $this->extraerEstilo($html1);
                $cuerpo1 = $this->extraerCuerpo($html1);
                $cuerpo2 = $this->extraerCuerpo($html2);
                $htmlFinal = $estilo . $cuerpo1 . '<pagebreak />' . $cuerpo2;
            } elseif ($html1) {
                $htmlFinal = $html1;
            } else {
                $htmlFinal = $html2;
            }

            $pdfStr = PDF::loadHTML($htmlFinal, $config)->output();
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('buscar_error', 'Error al generar el PDF. Intente de nuevo.');
        }

        $token = (string) Str::uuid();
        cache()->put('retencion_pdf_' . $token, base64_encode($pdfStr), now()->addMinutes(30));

        return redirect()->route('retencion')->with([
            'pdf_token'    => $token,
            'pdf_count'    => $encontrados,
            'pdf_anio_sel' => $request->anio_id,
            'pdf_nit_sel'  => $nitBuscado,
        ]);
    }

    //sirve el PDF almacenado en cache para visualizacion inline en el iframe
    public function verPdf(string $token)
    {
        $encoded = cache()->get('retencion_pdf_' . $token);
        if (!$encoded) {
            abort(404);
        }
        return response(base64_decode($encoded), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="constancia.pdf"',
        ]);
    }

    //retorna la fila donde el NIT/DUI de la columna C coincide con el buscado
    private function buscarNit($sheet, string $nit): ?array
    {
        $normalizado = $this->normNit($nit);
        $highestRow  = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            $celda = (string) $sheet->getCell('C' . $row)->getValue();
            if ($celda === '') continue;
            if ($this->normNit($celda) === $normalizado) {
                return [
                    'A' => (string) $sheet->getCell('A' . $row)->getValue(),
                    'B' => (string) $sheet->getCell('B' . $row)->getValue(),
                    'C' => $celda,
                    'D' => (string) $sheet->getCell('D' . $row)->getValue(),
                    'E' => $sheet->getCell('E' . $row)->getValue(),
                    'F' => $sheet->getCell('F' . $row)->getValue(),
                    'G' => $sheet->getCell('G' . $row)->getValue(),
                    'H' => $sheet->getCell('H' . $row)->getValue(),
                    'I' => $sheet->getCell('I' . $row)->getValue(),
                    'J' => $sheet->getCell('J' . $row)->getValue(),
                    'K' => $sheet->getCell('K' . $row)->getValue(),
                    'L' => $sheet->getCell('L' . $row)->getValue(),
                ];
            }
        }
        return null;
    }

    //normaliza el NIT/DUI quitando guiones y espacios para comparacion sin distinguir mayusculas
    private function normNit(string $s): string
    {
        return strtoupper(preg_replace('/[\s\-\.]/u', '', $s));
    }

    private function numVal($val): float
    {
        return is_numeric($val) ? (float) $val : 0.0;
    }

    private function extraerCuerpo(string $html): string
    {
        if (preg_match('/<body[^>]*>(.*?)<\/body>/si', $html, $m)) {
            return $m[1];
        }
        return $html;
    }

    private function extraerEstilo(string $html): string
    {
        if (preg_match('/<style[^>]*>(.*?)<\/style>/si', $html, $m)) {
            return '<style>' . $m[1] . '</style>';
        }
        return '';
    }
}
