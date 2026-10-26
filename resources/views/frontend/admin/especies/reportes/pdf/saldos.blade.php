{{--PDF: CONTROL DE SALDOS - existencias del distrito al final del mes, con la plantilla del distrito.
     PRECIO DE VTA. = existencia x precio de venta (en blanco si no se vende); EXISTENCIAS = existencia x costo--}}
@php
  $tituloReporte      = 'Control de Saldos';
  $encabezadoCaja     = 'CONTROL DE SALDOS';
  $codigoFormulario   = config('especies.codigos.saldos');
  $versionFormulario  = config('especies.versiones.saldos');
  $vigenciaFormulario = config('especies.vigencia');
  $encabezadoCentrado = true;
  $sinFechaGeneracion = true;
  $lineasTitulo = ['ALCALDIA MUNICIPAL DE SANTA ANA NORTE', $distrito->nombre_reporte,
                   'ESPECIES MUNICIPALES SALDOS A ' . mb_strtoupper($nombreMes) . ' DE ' . $hasta->year, 'CONTROL DE SALDOS'];

  //montos a 2 decimales (ya vienen truncados al centavo); el cero se muestra "$ -" como en tesoreria
  $dinero = function ($v) {
      if ($v === null) return '';
      if (abs($v) < 0.000001) return '$ -';
      return ($v < 0 ? '-$' : '$') . number_format(abs($v), 2);
  };
  //costo con al menos 2 decimales y hasta 4 (0.30, 0.075, 0.0105)
  $costo = function ($v) {
      if ($v === null) return '';
      [$entero, $dec] = explode('.', rtrim(number_format($v, 4), '0')) + [1 => ''];
      return '$' . $entero . '.' . str_pad($dec, 2, '0');
  };
@endphp
@include('frontend.admin.especies.reportes.pdf.header')

@if(empty($grupos))
  <p class="center">Sin especies registradas para este distrito en el mes.</p>
@else
<table class="datos">
  <thead>
    <tr>
      <th class="center" style="width:12%">CANTIDAD</th>
      <th style="width:40%">DESCRIPCION</th>
      <th class="right" style="width:14%">P.DE COSTO</th>
      <th class="right" style="width:17%">PRECIO DE VTA.</th>
      <th class="right" style="width:17%">EXISTENCIAS</th>
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
        <td class="right">{{ $dinero($f['existencias']) }}</td>
      </tr>
      @endforeach
      <tr>
        <td class="center"><strong>TOTAL</strong></td>
        <td></td>
        <td></td>
        <td class="right"><strong>{{ $dinero($g['total_venta'] ?? 0) }}</strong></td>
        <td class="right"><strong>{{ $dinero($g['total_existencias']) }}</strong></td>
      </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td colspan="3" class="right">TOTALES GENERALES</td>
      <td class="right">{{ $dinero($totalVtaGeneral) }}</td>
      <td class="right">{{ $dinero($totalExistenciasGeneral) }}</td>
    </tr>
  </tfoot>
</table>

@if($faltaCosto)
<p class="sub" style="margin-top:6px">
  <strong>Aviso:</strong> hay especies sin precio de costo; sus existencias a costo salen en cero.
  Complételo en Configuración &gt; Distritos.
</p>
@endif
@endif

@include('frontend.admin.especies.reportes.pdf.firma')

</body>
</html>
