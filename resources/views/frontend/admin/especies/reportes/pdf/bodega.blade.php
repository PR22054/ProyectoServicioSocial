{{-- PDF: EXISTENCIAS EN BODEGA - TABLA DE LOTES CON STOCK DISPONIBLE AL CORTE --}}
@php $tituloReporte = 'Existencias en Bodega'; @endphp
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
      <th>#</th>
      <th>Factura</th>
      <th>Serie</th>
      <th class="center">Rangos</th>
      <th class="right">Total en lote</th>
      <th class="right">Trasladado</th>
      <th class="right">Disponible</th>
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
          {{ number_format($r->numero_inicio) }} – {{ number_format($r->numero_fin) }}<br>
        @endforeach
      </td>
      <td class="right">{{ number_format($row['total']) }}</td>
      <td class="right">{{ number_format($row['total'] - $row['disponible']) }}</td>
      <td class="right"><strong>{{ number_format($row['disponible']) }}</strong></td>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td colspan="4" class="right">TOTAL EN BODEGA:</td>
      <td class="right">{{ number_format($lotes->sum('total')) }}</td>
      <td class="right">{{ number_format($lotes->sum(fn($l) => $l['total'] - $l['disponible'])) }}</td>
      <td class="right">{{ number_format($lotes->sum('disponible')) }}</td>
    </tr>
  </tfoot>
</table>
@endif

<br>
<p style="font-size:9px; text-align:right;">Generado el {{ now()->format('d/m/Y H:i') }}</p>

</body>
</html>
