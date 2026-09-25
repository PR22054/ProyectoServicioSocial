{{-- PDF: EXISTENCIAS POR DISTRITO - existencia al corte por detalle, valuada a precio de venta --}}
@php $tituloReporte = 'Libro de Existencias por Distrito'; @endphp
@include('frontend.admin.especies.reportes.pdf._header')

<p class="label">
  Distrito: {{ $distrito->nombre }} ({{ $distrito->codigo }})
  &nbsp;|&nbsp; Tipo: {{ $tipo->nombre }}
  &nbsp;|&nbsp; Fecha de corte: {{ $fechaCorte->format('d/m/Y') }}
</p>
<br>

@if($rows->isEmpty())
  <p class="center">Sin existencias registradas para los filtros seleccionados.</p>
@else
<table class="datos">
  <thead>
    <tr>
      <th class="center" style="width:4%">#</th>
      <th style="width:11%">FACTURA</th>
      <th class="center" style="width:6%">SERIE</th>
      <th class="right" style="width:9%">DEL</th>
      <th class="right" style="width:9%">AL</th>
      <th class="right" style="width:8%">VALOR</th>
      <th class="right" style="width:8%">RECIB.</th>
      <th class="right" style="width:7%">REAL.</th>
      <th class="right" style="width:7%">NULAS</th>
      <th class="right" style="width:7%">SALIDAS</th>
      <th class="right" style="width:8%">EXIST.</th>
      <th class="right" style="width:11%">SALDO</th>
    </tr>
  </thead>
  <tbody>
    @foreach($rows->values() as $i => $row)
    <tr class="{{ $i % 2 == 1 ? 'alt' : '' }}">
      <td class="center">{{ $i + 1 }}</td>
      <td>{{ $row['detalle']->lote->compra->numero_factura ?? '—' }}</td>
      <td class="center">{{ $row['detalle']->lote->serie ?: '—' }}</td>
      <td class="right">{{ number_format($row['detalle']->numero_inicio) }}</td>
      <td class="right">{{ number_format($row['detalle']->numero_fin) }}</td>
      <td class="right">{{ number_format($row['valor'], 2) }}</td>
      <td class="right">{{ number_format($row['detalle']->cantidad) }}</td>
      <td class="right">{{ number_format($row['realizado']) }}</td>
      <td class="right">{{ number_format($row['anulado']) }}</td>
      <td class="right">{{ number_format($row['salido']) }}</td>
      <td class="right"><strong>{{ number_format($row['disponible']) }}</strong></td>
      <td class="right"><strong>{{ number_format($row['saldo'], 2) }}</strong></td>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td colspan="6" class="right">TOTALES:</td>
      <td class="right">{{ number_format($rows->sum(fn($r) => $r['detalle']->cantidad)) }}</td>
      <td class="right">{{ number_format($rows->sum('realizado')) }}</td>
      <td class="right">{{ number_format($rows->sum('anulado')) }}</td>
      <td class="right">{{ number_format($rows->sum('salido')) }}</td>
      <td class="right">{{ number_format($rows->sum('disponible')) }}</td>
      <td class="right">{{ number_format($rows->sum('saldo'), 2) }}</td>
    </tr>
  </tfoot>
</table>

<br>
<p class="sub">
  <strong>EXIST.</strong> = recibido &minus; realizado &minus; nulas &minus; salidas.
  <strong>SALDO</strong> = existencia &times; valor unitario.
  Realizaciones acumuladas al corte: {{ number_format($realizadoTotal) }} documentos.
</p>
@endif

<br>
<p style="font-size:9px; text-align:right;">Generado el {{ now()->format('d/m/Y H:i') }}</p>

</body>
</html>
