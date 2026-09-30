{{-- PDF: ESPECIES MUNICIPALES REALIZADAS - con la plantilla del distrito: sus tipos, rotulos, costos y precios.
     PRECIO DE VTA. queda en blanco en lo que no se vende; DESCARGOS = cantidad x costo, incluidas las nulas --}}
@php
  $tituloReporte = 'Especies Municipales Realizadas';
  $sinRecuadro   = true;
  $sinNit        = true;

  // al menos dos decimales, y hasta cuatro cuando hacen falta (0.015 x 1261 = 18.915)
  $dinero = function ($v) {
      if ($v === null) return '';
      [$entero, $dec] = explode('.', rtrim(number_format($v, 4), '0')) + [1 => ''];
      return $entero . '.' . str_pad($dec, 2, '0');
  };
  $costo = fn($v) => $v === null ? '' : rtrim(rtrim(number_format($v, 4), '0'), '.');
@endphp
@include('frontend.admin.especies.reportes.pdf._header')

<p class="titulo">ALCALDIA MUNICIPAL DE SANTA ANA NORTE</p>
<p class="titulo">{{ $distrito->nombre_reporte }}</p>
<p class="titulo">ESPECIES MUNICIPALES REALIZADAS</p>
<p class="titulo">{{ $periodo }}</p>
<br>

@if(empty($grupos))
  <p class="center">Sin especies registradas para este distrito en el periodo.</p>
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
        <td class="center"><strong>TOTAL</strong></td>
        <td></td>
        <td></td>
        <td class="right"><strong>{{ $dinero($g['total_venta']) }}</strong></td>
        <td class="right"><strong>{{ $dinero($g['total_descargo']) }}</strong></td>
      </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td colspan="3" class="right">TOTALES GENERALES</td>
      <td class="right">{{ $dinero($totalVtaGeneral) }}</td>
      <td class="right">{{ $dinero($totalDescargoGeneral) }}</td>
    </tr>
  </tfoot>
</table>

@if($faltaCosto)
<p class="sub" style="margin-top:6px">
  <strong>Aviso:</strong> hay especies sin precio de costo; sus descargos salen en cero.
  Complételo en Configuración &gt; Tarifas por distrito.
</p>
@endif
@endif

<table class="firmas">
  <tr><td>{{ $firma['nombre'] }}<br>{{ $firma['cargo'] }}</td></tr>
</table>

<p style="font-size:8px; text-align:right; margin-top:10px;">Generado el {{ now()->format('d/m/Y H:i') }}</p>

</body>
</html>
