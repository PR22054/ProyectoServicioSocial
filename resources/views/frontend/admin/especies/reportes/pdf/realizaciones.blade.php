{{-- PDF: ESPECIES REALIZADAS DEL MES - hoja "ESPECIES MUNICIPALES REALIZADAS" de Tesoreria, con la plantilla del distrito.
     PRECIO DE VTA. = lo cobrado (en blanco si la especie no se vende); DESCARGOS = cantidad x costo --}}
@php
  $tituloReporte      = 'Especies Realizadas';
  $encabezadoCaja     = 'CONTROL DE ESPECIES REALIZADAS';
  $codigoFormulario   = config('especies.codigos.realizaciones');
  $versionFormulario  = config('especies.versiones.realizaciones');
  $vigenciaFormulario = config('especies.vigencia');
  $encabezadoCentrado = true;
  $sinFechaGeneracion = true;
  $lineasTitulo = ['ALCALDIA MUNICIPAL DE SANTA ANA NORTE', $distrito->nombre_reporte, 'ESPECIES MUNICIPALES REALIZADAS',
                   mb_strtoupper("$nombreMes $anio")];

  // montos a 2 decimales (los descargos ya vienen truncados al centavo); el cero se muestra "$ -" como en Tesoreria
  $dinero = function ($v) {
      if ($v === null) return '';
      if (abs($v) < 0.000001) return '$ -';
      return ($v < 0 ? '-$' : '$') . number_format(abs($v), 2);
  };
  // costo con al menos 2 decimales y hasta 4 (0.30, 0.075, 0.0105)
  $costo = function ($v) {
      if ($v === null) return '';
      [$entero, $dec] = explode('.', rtrim(number_format($v, 4), '0')) + [1 => ''];
      return '$' . $entero . '.' . str_pad($dec, 2, '0');
  };
@endphp
@include('frontend.admin.especies.reportes.pdf.header')

@if(empty($grupos))
  <p class="center">Sin especies registradas para este distrito.</p>
@else
<table class="datos">
  <thead>
    <tr>
      <th class="center" style="width:12%">CANTIDAD</th>
      <th style="width:40%">DESCRIPCION</th>
      <th class="right" style="width:14%">P.DE COSTO</th>
      <th class="right" style="width:17%">PRECIO DE VTA.</th>
      <th class="right" style="width:17%">DESCARGOS</th>
    </tr>
  </thead>
  <tbody>
    @foreach($grupos as $g)
      <tr><td colspan="5"><strong>{{ $g['titulo'] }}</strong></td></tr>
      @foreach($g['filas'] as $f)
      <tr>
        <td class="center">{{ number_format($f['cantidad']) }}</td>
        <td>{{ $f['descripcion'] }}</td>
        <td class="right">{{ $costo($f['costo']) }}</td>
        <td class="right">{{ $dinero($f['venta']) }}</td>
        <td class="right">{{ $dinero($f['descargo']) }}</td>
      </tr>
      @endforeach
      <tr>
        <td colspan="3" class="right"><strong>TOTAL</strong></td>
        <td class="right"><strong>{{ $dinero($g['total_venta']) }}</strong></td>
        <td class="right"><strong>{{ $dinero($g['total_descargo']) }}</strong></td>
      </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td colspan="3" class="right">TOTALES GENERALES</td>
      <td class="right">{{ $dinero($totalVenta) }}</td>
      <td class="right">{{ $dinero($totalDescargo) }}</td>
    </tr>
  </tfoot>
</table>

@if($faltaCosto)
<p class="sub" style="margin-top:6px">
  <strong>Aviso:</strong> hay especies realizadas sin precio de costo; su descargo sale en cero.
  Complételo en Configuración &gt; Distritos.
</p>
@endif
@endif

@include('frontend.admin.especies.reportes.pdf.firma')

</body>
</html>
