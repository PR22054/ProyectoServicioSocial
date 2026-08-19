{{-- PDF: EXISTENCIAS POR DISTRITO - TABLA DE DETALLES DE TRASLADO CON ANULADOS Y DISPONIBLES --}}
@php $tituloReporte = 'Existencias por Distrito'; @endphp
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
      <th>#</th>
      <th>Traslado</th>
      <th>Factura</th>
      <th class="center">Del</th>
      <th class="center">Al</th>
      <th class="right">Recibido</th>
      <th class="right">Anulado</th>
      <th class="right">Disponible</th>
    </tr>
  </thead>
  <tbody>
    @foreach($rows->values() as $i => $row)
    <tr class="{{ $i % 2 == 1 ? 'alt' : '' }}">
      <td class="center">{{ $i + 1 }}</td>
      <td class="center">#{{ $row['detalle']->traslado_id }}</td>
      <td>{{ $row['detalle']->lote->compra->numero_factura ?? '—' }}</td>
      <td class="right">{{ number_format($row['detalle']->numero_inicio) }}</td>
      <td class="right">{{ number_format($row['detalle']->numero_fin) }}</td>
      <td class="right">{{ number_format($row['detalle']->cantidad) }}</td>
      <td class="right">{{ number_format($row['anulado']) }}</td>
      <td class="right"><strong>{{ number_format($row['disponible']) }}</strong></td>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td colspan="5" class="right">TOTAL:</td>
      <td class="right">{{ number_format($rows->sum(fn($r) => $r['detalle']->cantidad)) }}</td>
      <td class="right">{{ number_format($rows->sum('anulado')) }}</td>
      <td class="right">{{ number_format($rows->sum('disponible')) }}</td>
    </tr>
  </tfoot>
</table>

<br>
<p class="sub">
  <strong>Nota:</strong> Realizaciones acumuladas al corte:
  <strong>{{ number_format($realizadoTotal) }}</strong> documentos
  (estas ya fueron descontadas del inventario original de bodega).
</p>
@endif

<br>
<p style="font-size:9px; text-align:right;">Generado el {{ now()->format('d/m/Y H:i') }}</p>

</body>
</html>
