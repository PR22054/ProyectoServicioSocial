{{-- PDF: CONTROL DE SALDOS - REALIZACIONES POR TIPO Y DENOMINACION EN EL PERIODO --}}
@php $tituloReporte = 'Control de Saldos'; @endphp
@include('frontend.admin.especies.reportes.pdf._header')

<p class="label">
  Distrito: {{ $distrito->nombre }} ({{ $distrito->codigo }})
  &nbsp;|&nbsp; Del {{ $desde->format('d/m/Y') }} al {{ $hasta->format('d/m/Y') }}
</p>
<br>

@if($grupos->isEmpty())
  <p class="center">Sin realizaciones registradas para los filtros seleccionados.</p>
@else

  @foreach($grupos as $grupo)
  <p class="seccion">{{ strtoupper($grupo['tipo']->nombre) }}</p>
  <table class="datos">
    <thead>
      <tr>
        <th class="center" style="width:15%">Cantidad</th>
        <th style="width:30%">Descripcion</th>
        <th class="right" style="width:20%">Precio unitario</th>
        <th class="right" style="width:35%">Monto cobrado</th>
      </tr>
    </thead>
    <tbody>
      @foreach($grupo['denoms'] as $i => $d)
      <tr class="{{ $i % 2 == 1 ? 'alt' : '' }}">
        <td class="center">{{ number_format($d['cantidad']) }}</td>
        <td>$ {{ number_format($d['denominacion']->valor, 2) }}</td>
        <td class="right">$ {{ number_format($d['denominacion']->valor, 2) }}</td>
        <td class="right">$ {{ number_format($d['monto'], 2) }}</td>
      </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td class="center">{{ number_format($grupo['total_cantidad']) }}</td>
        <td colspan="2" class="right">SUBTOTAL {{ strtoupper($grupo['tipo']->nombre) }}:</td>
        <td class="right">$ {{ number_format($grupo['total_monto'], 2) }}</td>
      </tr>
    </tfoot>
  </table>
  @endforeach

  <table class="datos" style="margin-top:12px;">
    <tfoot>
      <tr>
        <td class="center"><strong>{{ number_format($totalCantidad) }}</strong></td>
        <td colspan="2" class="right"><strong>TOTALES GENERALES:</strong></td>
        <td class="right"><strong>$ {{ number_format($totalGeneral, 2) }}</strong></td>
      </tr>
    </tfoot>
  </table>

@endif

<br>
<p style="font-size:9px; text-align:right;">Generado el {{ now()->format('d/m/Y H:i') }}</p>

</body>
</html>
