{{-- PDF: HISTORIAL DE TRASLADOS - TABLA DE DETALLES DE TRASLADO EN EL PERIODO --}}
@php $tituloReporte = 'Historial de Traslados'; @endphp
@include('frontend.admin.especies.reportes.pdf._header')

<p class="label">
  Distrito: {{ $distrito->nombre }} ({{ $distrito->codigo }})
  &nbsp;|&nbsp; Tipo: {{ $tipo->nombre }}
  &nbsp;|&nbsp; Del {{ $desde->format('d/m/Y') }} al {{ $hasta->format('d/m/Y') }}
</p>
<br>

@if($detalles->isEmpty())
  <p class="center">Sin traslados registrados para los filtros seleccionados.</p>
@else
<table class="datos">
  <thead>
    <tr>
      <th>#</th>
      <th>Traslado</th>
      <th>Fecha traslado</th>
      <th>Factura</th>
      <th>Serie</th>
      <th class="center">Del</th>
      <th class="center">Al</th>
      <th class="right">Cantidad</th>
    </tr>
  </thead>
  <tbody>
    @foreach($detalles as $i => $d)
    <tr class="{{ $i % 2 == 1 ? 'alt' : '' }}">
      <td class="center">{{ $i + 1 }}</td>
      <td class="center">#{{ $d->traslado_id }}</td>
      <td class="center">{{ $d->traslado->fecha->format('d/m/Y') }}</td>
      <td>{{ $d->lote->compra->numero_factura ?? '—' }}</td>
      <td class="center">{{ $d->lote->serie ?: '—' }}</td>
      <td class="right">{{ number_format($d->numero_inicio) }}</td>
      <td class="right">{{ number_format($d->numero_fin) }}</td>
      <td class="right">{{ number_format($d->cantidad) }}</td>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td colspan="7" class="right">TOTAL TRASLADADO:</td>
      <td class="right">{{ number_format($detalles->sum('cantidad')) }}</td>
    </tr>
  </tfoot>
</table>
@endif

<br>
<p style="font-size:9px; text-align:right;">Generado el {{ now()->format('d/m/Y H:i') }}</p>

</body>
</html>
