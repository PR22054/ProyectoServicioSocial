@php $tituloReporte = 'Libro de Especies'; @endphp
@include('frontend.admin.especies.reportes.pdf._header')

<p class="label">
  Distrito: {{ $distrito->nombre }} ({{ $distrito->codigo }})
  &nbsp;|&nbsp; Tipo: {{ $tipo->nombre }}
  &nbsp;|&nbsp; Período: {{ $nombreMes }} {{ $anio }}
  @if($denomFiltro) &nbsp;|&nbsp; Denominación: ${{ number_format($denomFiltro->valor, 2) }} @endif
</p>
<br>

{{-- SALDO INICIAL --}}
<p class="seccion">SALDO INICIAL AL 01 DE {{ strtoupper($nombreMes) }} DE {{ $anio }}</p>
<table class="datos">
  <tr><th style="width:60%">Concepto</th><th class="right">Cantidad</th></tr>
  <tr>
    <td>Documentos disponibles en existencia al inicio del período</td>
    <td class="right">{{ number_format($saldoInicio) }}</td>
  </tr>
</table>

{{-- TRASLADOS RECIBIDOS --}}
<p class="seccion">TRASLADOS RECIBIDOS EN EL MES</p>
@if($trasladosMes->isEmpty())
  <p class="sub" style="margin-left:8px">Sin traslados recibidos en este período.</p>
@else
<table class="datos">
  <thead>
    <tr>
      <th>Traslado #</th><th>Fecha</th><th>Factura</th>
      <th class="center">Del</th><th class="center">Al</th><th class="right">Cantidad</th>
    </tr>
  </thead>
  <tbody>
    @foreach($trasladosMes as $i => $d)
    <tr class="{{ $i % 2 == 1 ? 'alt' : '' }}">
      <td class="center">{{ $d->traslado_id }}</td>
      <td class="center">{{ $d->traslado->fecha->format('d/m/Y') }}</td>
      <td>{{ $d->lote->compra->numero_factura ?? '—' }}</td>
      <td class="right">{{ number_format($d->numero_inicio) }}</td>
      <td class="right">{{ number_format($d->numero_fin) }}</td>
      <td class="right">{{ number_format($d->cantidad) }}</td>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td colspan="5" class="right">TOTAL RECIBIDO:</td>
      <td class="right">{{ number_format($trasladosMes->sum('cantidad')) }}</td>
    </tr>
  </tfoot>
</table>
@endif

{{-- REALIZACIONES --}}
<p class="seccion">REALIZACIONES DEL MES</p>
@if($realizacionesMes->isEmpty())
  <p class="sub" style="margin-left:8px">Sin realizaciones en este período.</p>
@else
<table class="datos">
  <thead>
    <tr>
      <th>Fecha</th><th>Denominación</th>
      <th class="center">Del</th><th class="center">Al</th>
      <th class="right">Cantidad</th><th class="right">Monto</th>
    </tr>
  </thead>
  <tbody>
    @foreach($realizacionesMes as $i => $r)
    <tr class="{{ $i % 2 == 1 ? 'alt' : '' }}">
      <td class="center">{{ $r->fecha->format('d/m/Y') }}</td>
      <td class="center">${{ number_format($r->denominacion->valor ?? 0, 2) }}</td>
      <td class="right">{{ number_format($r->numero_inicio) }}</td>
      <td class="right">{{ number_format($r->numero_fin) }}</td>
      <td class="right">{{ number_format($r->cantidad) }}</td>
      <td class="right">${{ number_format($r->monto_cobrado, 2) }}</td>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td colspan="4" class="right">TOTAL REALIZACIONES:</td>
      <td class="right">{{ number_format($realizacionesMes->sum('cantidad')) }}</td>
      <td class="right">${{ number_format($realizacionesMes->sum('monto_cobrado'), 2) }}</td>
    </tr>
  </tfoot>
</table>
@endif

{{-- NULAS --}}
<p class="seccion">NULAS DEL MES</p>
@if($nulasMes->isEmpty())
  <p class="sub" style="margin-left:8px">Sin anulaciones en este período.</p>
@else
<table class="datos">
  <thead>
    <tr>
      <th>Fecha</th><th class="center">Del</th><th class="center">Al</th>
      <th class="right">Cantidad</th><th>Motivo</th>
    </tr>
  </thead>
  <tbody>
    @foreach($nulasMes as $i => $n)
    <tr class="{{ $i % 2 == 1 ? 'alt' : '' }}">
      <td class="center">{{ $n->fecha->format('d/m/Y') }}</td>
      <td class="right">{{ number_format($n->numero_inicio) }}</td>
      <td class="right">{{ number_format($n->numero_fin) }}</td>
      <td class="right">{{ number_format($n->numero_fin - $n->numero_inicio + 1) }}</td>
      <td>{{ $n->motivo ?? '—' }}</td>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td colspan="3" class="right">TOTAL NULAS:</td>
      <td class="right">{{ number_format($nulasMes->sum(fn($n) => $n->numero_fin - $n->numero_inicio + 1)) }}</td>
      <td></td>
    </tr>
  </tfoot>
</table>
@endif

{{-- SALDO FINAL --}}
@php
  $saldoFinal = $saldoInicio + $trasladosMes->sum('cantidad')
                - $realizacionesMes->sum('cantidad')
                - $nulasMes->sum(fn($n) => $n->numero_fin - $n->numero_inicio + 1);
@endphp
<p class="seccion">SALDO A NUEVA CUENTA AL 31 DE {{ strtoupper($nombreMes) }} DE {{ $anio }}</p>
<table class="datos">
  <tr>
    <td style="width:60%">Documentos disponibles para el siguiente período</td>
    <td class="right"><strong>{{ number_format($saldoFinal) }}</strong></td>
  </tr>
</table>

<br>
<p style="font-size:9px; text-align:right;">Generado el {{ now()->format('d/m/Y H:i') }}</p>

</body>
</html>
