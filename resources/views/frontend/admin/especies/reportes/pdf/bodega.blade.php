{{-- PDF: EXISTENCIAS EN BODEGA - lotes con stock disponible, valuados a precio de venta --}}
@php $tituloReporte = 'Libro de Existencias en Bodega'; @endphp
@include('frontend.admin.especies.reportes.pdf._header')

<p class="label">
  Tipo de especie: {{ $tipo->nombre }}
  &nbsp;|&nbsp; Del {{ $desde->format('d/m/Y') }} al {{ $hasta->format('d/m/Y') }}
</p>
<br>

@if($lotes->isEmpty())
  <p class="center">Sin existencias disponibles en bodega para los filtros seleccionados.</p>
@else
<table class="datos">
  <thead>
    <tr>
      <th class="center" style="width:4%">#</th>
      <th style="width:12%">FACTURA</th>
      <th class="center" style="width:7%">SERIE</th>
      <th style="width:19%">DEL / AL</th>
      <th class="right" style="width:9%">VALOR</th>
      <th class="right" style="width:8%">CANT.</th>
      <th class="right" style="width:8%">TRASL.</th>
      <th class="right" style="width:9%">EXIST.</th>
      <th class="right" style="width:12%">SALDO</th>
    </tr>
  </thead>
  <tbody>
    @foreach($lotes->values() as $i => $row)
    <tr class="{{ $i % 2 == 1 ? 'alt' : '' }}">
      <td class="center">{{ $i + 1 }}</td>
      <td>{{ $row['lote']->compra->numero_factura ?? '—' }}</td>
      <td class="center">{{ $row['lote']->serie ?: '—' }}</td>
      <td>
        @foreach($row['rangos'] as $r)
          {{ number_format($r->numero_inicio) }} / {{ number_format($r->numero_fin) }}<br>
        @endforeach
      </td>
      <td class="right">{{ number_format($row['valor'], 2) }}</td>
      <td class="right">{{ number_format($row['total']) }}</td>
      <td class="right">{{ number_format($row['total'] - $row['disponible']) }}</td>
      <td class="right"><strong>{{ number_format($row['disponible']) }}</strong></td>
      <td class="right"><strong>{{ number_format($row['monto_disponible'], 2) }}</strong></td>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td colspan="5" class="right">TOTAL EN BODEGA:</td>
      <td class="right">{{ number_format($lotes->sum('total')) }}</td>
      <td class="right">{{ number_format($lotes->sum(fn($l) => $l['total'] - $l['disponible'])) }}</td>
      <td class="right">{{ number_format($lotes->sum('disponible')) }}</td>
      <td class="right">{{ number_format($lotes->sum('monto_disponible'), 2) }}</td>
    </tr>
  </tfoot>
</table>

<br>
<p class="sub">
  <strong>SALDO</strong> = existencia &times; valor unitario de la denominación.
  Comprado en el período: {{ number_format($lotes->sum('total')) }} documentos
  por ${{ number_format($lotes->sum('monto_total'), 2) }}.
</p>
@endif

<br>
<p style="font-size:9px; text-align:right;">Generado el {{ now()->format('d/m/Y H:i') }}</p>

</body>
</html>
