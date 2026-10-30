{{--PDF: ESPECIES REALIZADAS DETALLADO - cada realizacion y cada nula del mes (denominacion, serie y rango), agrupadas por tipo, sin encabezado
     de formulario ni firma. las nulas no cobran; DESCARGO = cantidad x costo de la tarifa del distrito--}}
@php
  $tituloReporte = 'Especies Realizadas';
  $sinRecuadro   = true;
  $lineasTitulo  = ['ALCALDIA MUNICIPAL DE SANTA ANA NORTE', $distrito->nombre_reporte, 'ESPECIES MUNICIPALES REALIZADAS',
                    mb_strtoupper("$nombreMes $anio")];

  //montos con 2 o 3 decimales segun hagan falta (22.875); el cero se muestra "$ -"
  $dinero = function ($v) {
      $v = round($v, 3);
      if (abs($v) < 0.000001) return '$ -';
      return '$' . number_format($v, abs($v * 100 - round($v * 100)) > 0.000001 ? 3 : 2);
  };
@endphp
@include('frontend.admin.especies.reportes.pdf.header')

@if($grupos->isEmpty())
  <p class="center">Sin realizaciones ni nulas registradas en el mes.</p>
@else
<table class="datos">
  <thead>
    <tr>
      <th>Denominación</th>
      <th class="center">Serie</th>
      <th class="center">Del</th>
      <th class="center">Al</th>
      <th class="right">Cantidad</th>
      <th class="right">Monto cobrado</th>
      <th class="right">Descargo</th>
    </tr>
  </thead>
  <tbody>
    @foreach($grupos as $g)
      <tr><td colspan="7" style="background:#e8eff8;"><strong>{{ mb_strtoupper($g['tipo']->nombre) }}</strong></td></tr>
      @foreach(['filas' => null, 'nulas' => 'NULAS'] as $clave => $rotulo)
        @if($rotulo && $g[$clave])
        <tr><td colspan="7"><strong>{{ $rotulo }}</strong></td></tr>
        @endif
        @foreach($g[$clave] as $f)
        <tr class="{{ $loop->odd ? '' : 'alt' }}">
          <td class="center">${{ number_format($f['valor'], 2) }}</td>
          <td class="center">{{ $f['serie'] ?: '—' }}</td>
          <td class="right">{{ number_format($f['del']) }}</td>
          <td class="right">{{ number_format($f['al']) }}</td>
          <td class="right">{{ number_format($f['cantidad']) }}</td>
          <td class="right">{{ $dinero($f['cobrado']) }}</td>
          <td class="right">{{ $dinero($f['descargo']) }}</td>
        </tr>
        @endforeach
      @endforeach
      <tr>
        <td colspan="4" class="right"><strong>SUBTOTAL {{ mb_strtoupper($g['tipo']->nombre) }}:</strong></td>
        <td class="right"><strong>{{ number_format($g['cantidad']) }}</strong></td>
        <td class="right"><strong>{{ $dinero($g['cobrado']) }}</strong></td>
        <td class="right"><strong>{{ $dinero($g['descargo']) }}</strong></td>
      </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td colspan="4" class="right">TOTAL:</td>
      <td class="right">{{ number_format($totalCantidad) }}</td>
      <td class="right">{{ $dinero($totalCobrado) }}</td>
      <td class="right">{{ $dinero($totalDescargo) }}</td>
    </tr>
  </tfoot>
</table>

@if($faltaCosto)
<p class="sub" style="margin-top:6px">
  <strong>Aviso:</strong> hay especies sin precio de costo; su descargo sale en cero.
  Complételo en Configuración &gt; Distritos.
</p>
@endif
@endif

</body>
</html>
