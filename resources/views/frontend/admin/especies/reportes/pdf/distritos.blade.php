{{-- PDF: EXISTENCIAS POR DISTRITO - existencia al corte por lote, con sus rangos, valuada a precio de venta --}}
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
      <th style="width:10%">FACTURA</th>
      <th class="center" style="width:6%">SERIE</th>
      <th class="right" style="width:7%">VALOR</th>
      <th class="right" style="width:7%">RECIB.</th>
      <th class="right" style="width:7%">SALIDAS</th>
      <th class="right" style="width:7%">REAL.</th>
      <th class="right" style="width:6%">NULAS</th>
      <th class="right" style="width:7%">EXIST.</th>
      <th style="width:25%">RANGOS EN EXISTENCIA</th>
      <th class="right" style="width:10%">SALDO</th>
    </tr>
  </thead>
  <tbody>
    @foreach($rows as $i => $row)
    <tr class="{{ $i % 2 == 1 ? 'alt' : '' }}">
      <td class="center">{{ $i + 1 }}</td>
      <td>{{ $row['lote']->compra->numero_factura ?? '—' }}</td>
      <td class="center">{{ $row['lote']->serie ?: '—' }}</td>
      <td class="right">{{ number_format($row['valor'], 2) }}</td>
      <td class="right">{{ number_format($row['recibido']) }}</td>
      <td class="right">{{ number_format($row['salido']) }}</td>
      <td class="right">{{ number_format($row['realizado']) }}</td>
      <td class="right">{{ number_format($row['anulado']) }}</td>
      <td class="right"><strong>{{ number_format($row['cantidad']) }}</strong></td>
      <td>{{ \App\Services\Inventario::texto($row['intervalos']) ?: '—' }}</td>
      <td class="right"><strong>{{ number_format($row['monto'], 2) }}</strong></td>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td colspan="4" class="right">TOTALES:</td>
      <td class="right">{{ number_format($rows->sum('recibido')) }}</td>
      <td class="right">{{ number_format($rows->sum('salido')) }}</td>
      <td class="right">{{ number_format($rows->sum('realizado')) }}</td>
      <td class="right">{{ number_format($rows->sum('anulado')) }}</td>
      <td class="right">{{ number_format($rows->sum('cantidad')) }}</td>
      <td></td>
      <td class="right">{{ number_format($rows->sum('monto'), 2) }}</td>
    </tr>
  </tfoot>
</table>

<br>
<p class="sub">
  <strong>EXIST.</strong> = recibido &minus; salidas &minus; realizado &minus; nulas.
  <strong>SALDO</strong> = existencia &times; valor unitario.
</p>
@endif

<br>
<p style="font-size:9px; text-align:right;">Generado el {{ now()->format('d/m/Y H:i') }}</p>

</body>
</html>
