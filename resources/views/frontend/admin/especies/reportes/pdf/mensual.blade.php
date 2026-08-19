@php $tituloReporte = 'Reporte Mensual por Distrito'; @endphp
@include('frontend.admin.especies.reportes.pdf._header')

<p class="label center">{{ strtoupper($nombreMes) }} {{ $anio }}</p>
<br>

@if(empty($tabla))
  <p class="center">Sin movimientos registrados en este período.</p>
@else
<table class="datos">
  <thead>
    <tr>
      <th>Distrito</th>
      <th>Tipo de especie</th>
      <th class="right">Saldo inicial</th>
      <th class="right">Recibido</th>
      <th class="right">Realizado</th>
      <th class="right">Nulas</th>
      <th class="right">Saldo final</th>
      <th class="right">Monto cobrado</th>
    </tr>
  </thead>
  <tbody>
    @foreach($tabla as $i => $row)
    <tr class="{{ $i % 2 == 1 ? 'alt' : '' }}">
      <td>{{ $row['distrito']->nombre }} <span class="sub">({{ $row['distrito']->codigo }})</span></td>
      <td>{{ $row['tipo']->nombre }}</td>
      <td class="right">{{ number_format($row['saldo_inicio']) }}</td>
      <td class="right">{{ number_format($row['recibido']) }}</td>
      <td class="right">{{ number_format($row['realizado']) }}</td>
      <td class="right">{{ number_format($row['nulado']) }}</td>
      <td class="right"><strong>{{ number_format($row['saldo_final']) }}</strong></td>
      <td class="right">${{ number_format($row['monto_cobrado'], 2) }}</td>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td colspan="2" class="right">TOTALES:</td>
      <td class="right">{{ number_format(array_sum(array_column($tabla, 'saldo_inicio'))) }}</td>
      <td class="right">{{ number_format(array_sum(array_column($tabla, 'recibido'))) }}</td>
      <td class="right">{{ number_format(array_sum(array_column($tabla, 'realizado'))) }}</td>
      <td class="right">{{ number_format(array_sum(array_column($tabla, 'nulado'))) }}</td>
      <td class="right">{{ number_format(array_sum(array_column($tabla, 'saldo_final'))) }}</td>
      <td class="right">${{ number_format(array_sum(array_column($tabla, 'monto_cobrado')), 2) }}</td>
    </tr>
  </tfoot>
</table>
@endif

<br>
<p style="font-size:9px; text-align:right;">Generado el {{ now()->format('d/m/Y H:i') }}</p>

</body>
</html>
