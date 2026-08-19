{{-- PDF: REALIZACIONES POR PERIODO - TABLA DE DOCUMENTOS ENTREGADOS A CONTRIBUYENTES --}}
@php $tituloReporte = 'Realizaciones por Período'; @endphp
@include('frontend.admin.especies.reportes.pdf._header')

<p class="label">
  Distrito: {{ $distrito->nombre }} ({{ $distrito->codigo }})
  &nbsp;|&nbsp; Tipo: {{ $tipo->nombre }}
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
      <th class="center">Del</th>
      <th class="center">Al</th>
      <th class="right">Cantidad</th>
      <th class="right">Monto cobrado</th>
      <th>Contribuyente</th>
      <th>Registrado por</th>
    </tr>
  </thead>
  <tbody>
    @foreach($realizaciones as $i => $r)
    <tr class="{{ $i % 2 == 1 ? 'alt' : '' }}">
      <td class="center">{{ $i + 1 }}</td>
      <td class="center">{{ $r->fecha->format('d/m/Y') }}</td>
      <td class="center">${{ number_format($r->denominacion->valor ?? 0, 2) }}</td>
      <td class="right">{{ number_format($r->numero_inicio) }}</td>
      <td class="right">{{ number_format($r->numero_fin) }}</td>
      <td class="right">{{ number_format($r->cantidad) }}</td>
      <td class="right">${{ number_format($r->monto_cobrado, 2) }}</td>
      <td>{{ $r->nombre_contribuyente ?? '—' }}</td>
      <td>{{ $r->usuario->usuario ?? '—' }}</td>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td colspan="5" class="right">TOTAL:</td>
      <td class="right">{{ number_format($realizaciones->sum('cantidad')) }}</td>
      <td class="right">${{ number_format($realizaciones->sum('monto_cobrado'), 2) }}</td>
      <td colspan="2"></td>
    </tr>
  </tfoot>
</table>
@endif

<br>
<p style="font-size:9px; text-align:right;">Generado el {{ now()->format('d/m/Y H:i') }}</p>

</body>
</html>
