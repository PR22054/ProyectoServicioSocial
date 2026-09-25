{{-- PDF: ESPECIES MUNICIPALES REALIZADAS - por tipo y denominacion, con nulas y descargos a costo --}}
@php $tituloReporte = 'Especies Municipales Realizadas'; @endphp
@include('frontend.admin.especies.reportes.pdf._header')

<p class="label">
  Distrito: {{ $distrito->nombre }} ({{ $distrito->codigo }})
  &nbsp;|&nbsp; Del {{ $desde->format('d/m/Y') }} al {{ $hasta->format('d/m/Y') }}
</p>
<br>

@if($grupos->isEmpty())
  <p class="center">Sin especies registradas para este distrito.</p>
@else

<table class="datos">
  <thead>
    <tr>
      <th class="center" style="width:12%">CANTIDAD</th>
      <th style="width:38%">DESCRIPCION</th>
      <th class="right" style="width:14%">P.DE COSTO</th>
      <th class="right" style="width:18%">PRECIO DE VTA.</th>
      <th class="right" style="width:18%">DESCARGOS</th>
    </tr>
  </thead>
  <tbody>
    @foreach($grupos as $grupo)
      {{-- encabezado del tipo de especie --}}
      <tr>
        <td colspan="5"><strong>{{ strtoupper($grupo['tipo']->nombre) }}</strong></td>
      </tr>

      @foreach($grupo['filas'] as $f)
      <tr>
        <td class="center">{{ number_format($f['cantidad']) }}</td>
        <td>{{ $f['etiqueta'] }}</td>
        <td class="right">
          @if($f['costo'] !== null){{ rtrim(rtrim(number_format($f['costo'], 4), '0'), '.') }}@else—@endif
        </td>
        <td class="right">{{ number_format($f['precio_vta'], 2) }}</td>
        <td class="right">{{ number_format($f['descargo'], 2) }}</td>
      </tr>
      @endforeach

      {{-- subtotal del tipo: la venta solo suma lo realizado, el descargo tambien las nulas --}}
      <tr>
        <td class="center"><strong>{{ number_format($grupo['total_cantidad']) }}</strong></td>
        <td colspan="2" class="right"><strong>TOTAL</strong></td>
        <td class="right"><strong>{{ number_format($grupo['total_vta'], 2) }}</strong></td>
        <td class="right"><strong>{{ number_format($grupo['total_descargo'], 2) }}</strong></td>
      </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td class="center"><strong>{{ number_format($totalCantidad) }}</strong></td>
      <td colspan="2" class="right"><strong>TOTALES GENERALES</strong></td>
      <td class="right"><strong>{{ number_format($totalVtaGeneral, 2) }}</strong></td>
      <td class="right"><strong>{{ number_format($totalDescargoGeneral, 2) }}</strong></td>
    </tr>
  </tfoot>
</table>

<br>
<p class="sub">
  <strong>PRECIO DE VTA.</strong> = cantidad realizada &times; precio de venta.
  <strong>DESCARGOS</strong> = cantidad &times; precio de costo, e incluye las nulas.
  Las nulas se muestran con su valor facial pero no suman al total de venta porque no se cobraron.
</p>

@if($faltaCosto)
<p class="sub" style="margin-top:6px">
  <strong>Aviso:</strong> hay denominaciones sin precio de costo capturado; sus descargos salen en cero.
  Complételo en Configuración &gt; Denominaciones.
</p>
@endif

@endif

<br>
<p style="font-size:9px; text-align:right;">Generado el {{ now()->format('d/m/Y H:i') }}</p>

</body>
</html>
