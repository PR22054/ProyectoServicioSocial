{{-- PDF: REALIZACIONES POR PERIODO - documentos realizados por el distrito, de un tipo o de todos agrupados por tipo.
     DESCARGO = cantidad x costo de la tarifa del distrito --}}
@php
  $tituloReporte = 'Realizaciones por Período';
  $descargo = fn($r) => $r->cantidad * (float) ($costos[$r->denominacion_id] ?? 0);

  // al menos dos decimales, y hasta cuatro cuando hacen falta (0.015 x 7673 = 115.095)
  $dinero = function ($v) {
      [$entero, $dec] = explode('.', rtrim(number_format($v, 4), '0')) + [1 => ''];
      return $entero . '.' . str_pad($dec, 2, '0');
  };
  $n = 0;
@endphp
@include('frontend.admin.especies.reportes.pdf.header')

<p class="label">
  Distrito: {{ $distrito->nombre }} ({{ $distrito->codigo }})
  &nbsp;|&nbsp; Tipo: {{ $tipo?->nombre ?? 'Todas las especies' }}
  &nbsp;|&nbsp; Del {{ $desde->format('d/m/Y') }} al {{ $hasta->format('d/m/Y') }}
</p>
<br>

@if($realizaciones->isEmpty())
  <p class="center">Sin realizaciones registradas para los filtros seleccionados.</p>
@else
<table class="datos">
  <thead>
    <tr>
      <th>#</th>
      <th>Fecha</th>
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
    @foreach($grupos as $rs)
      @unless($tipo)
      <tr><td colspan="9" style="background:#e8eff8;"><strong>{{ mb_strtoupper($rs->first()->tipoEspecie->nombre) }}</strong></td></tr>
      @endunless
      @foreach($rs as $r)
      <tr class="{{ $n % 2 == 1 ? 'alt' : '' }}">
        <td class="center">{{ ++$n }}</td>
        <td class="center">{{ $r->fecha->format('d/m/Y') }}</td>
        <td class="center">${{ number_format($r->denominacion->valor ?? 0, 2) }}</td>
        <td class="center">{{ $r->serie ?: '—' }}</td>
        <td class="right">{{ number_format($r->numero_inicio) }}</td>
        <td class="right">{{ number_format($r->numero_fin) }}</td>
        <td class="right">{{ number_format($r->cantidad) }}</td>
        <td class="right">${{ number_format($r->monto_cobrado, 2) }}</td>
        <td class="right">${{ $dinero($descargo($r)) }}</td>
      </tr>
      @endforeach
      @unless($tipo)
      <tr>
        <td colspan="6" class="right"><strong>SUBTOTAL {{ mb_strtoupper($rs->first()->tipoEspecie->nombre) }}:</strong></td>
        <td class="right"><strong>{{ number_format($rs->sum('cantidad')) }}</strong></td>
        <td class="right"><strong>${{ number_format($rs->sum('monto_cobrado'), 2) }}</strong></td>
        <td class="right"><strong>${{ $dinero($rs->sum($descargo)) }}</strong></td>
      </tr>
      @endunless
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td colspan="6" class="right">TOTAL:</td>
      <td class="right">{{ number_format($realizaciones->sum('cantidad')) }}</td>
      <td class="right">${{ number_format($realizaciones->sum('monto_cobrado'), 2) }}</td>
      <td class="right">${{ $dinero($realizaciones->sum($descargo)) }}</td>
    </tr>
  </tfoot>
</table>

@if($costos->contains(null))
<p class="sub" style="margin-top:6px">
  <strong>Aviso:</strong> hay especies sin precio de costo; su descargo sale en cero.
  Complételo en Configuración &gt; Distritos.
</p>
@endif
@endif

@include('frontend.admin.especies.reportes.pdf.firma')

</body>
</html>
