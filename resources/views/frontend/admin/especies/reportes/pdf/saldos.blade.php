{{-- PDF: CONTROL DE SALDOS - existencias del distrito al final del periodo, con la plantilla del distrito.
     PRECIO DE VTA. = existencia x precio de venta (en blanco si no se vende); EXISTENCIAS = existencia x costo --}}
@php
  $tituloReporte    = 'Control de Saldos';
  $codigoFormulario = config('especies.codigos.saldos');

  // montos a 2 decimales redondeados como Excel: primero a 15 cifras y luego mitad hacia arriba
  // (340187 x 0.015 = 5102.805 -> 5,102.81); los totales suman los montos sin redondear
  $dinero = function ($v) {
      if ($v === null) return '';
      $s = sprintf('%.15g', abs($v));
      if (stripos($s, 'e') !== false) return '$' . number_format($v, 2);
      [$entero, $dec] = explode('.', $s) + [1 => ''];
      $dec      = str_pad($dec, 3, '0');
      $centavos = (int) ($entero . substr($dec, 0, 2)) + ($dec[2] >= '5' ? 1 : 0);
      return ($v < 0 ? '-$' : '$') . number_format(intdiv($centavos, 100)) . '.' . str_pad($centavos % 100, 2, '0', STR_PAD_LEFT);
  };
  // costo con al menos 2 decimales y hasta 4 (0.30, 0.075, 0.0105)
  $costo = function ($v) {
      if ($v === null) return '';
      [$entero, $dec] = explode('.', rtrim(number_format($v, 4), '0')) + [1 => ''];
      return '$' . $entero . '.' . str_pad($dec, 2, '0');
  };
@endphp
@include('frontend.admin.especies.reportes.pdf.header')

<p class="label">
  Distrito: {{ $distrito->nombre }} ({{ $distrito->codigo }})
  &nbsp;|&nbsp; Tipo: Todas las especies
  &nbsp;|&nbsp; Del {{ $desde->format('d/m/Y') }} al {{ $hasta->format('d/m/Y') }}
</p>
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
        <td class="right"><strong>{{ $dinero($g['total_venta']) }}</strong></td>
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
