{{-- PDF: CONSOLIDADO ANUAL - matriz meses x denominaciones, realizado y nulas --}}
@php $tituloReporte = 'Consolidado Anual por Distrito'; @endphp
@include('frontend.admin.especies.reportes.pdf._header')

<p class="label">
  {{ strtoupper($tipo->nombre) }}
  &nbsp;|&nbsp; Distrito: {{ $distrito->nombre }} ({{ $distrito->codigo }})
  &nbsp;|&nbsp; Año {{ $anio }}
</p>
<br>

@if($denoms->isEmpty())
  <p class="center">Este tipo de especie no tiene denominaciones activas.</p>
@else

<p class="seccion">REALIZADO</p>
<table class="datos">
  <thead>
    <tr>
      <th style="width:12%">MES</th>
      @foreach($denoms as $d)
        <th class="right">{{ $d->descripcion ?: number_format($d->valor, 2) }}</th>
      @endforeach
      <th class="right">TOTAL</th>
      <th class="right">MONTO</th>
    </tr>
  </thead>
  <tbody>
    @foreach($tablaReal['filas'] as $i => $f)
    <tr class="{{ $i % 2 == 1 ? 'alt' : '' }}">
      <td>{{ strtoupper($meses[$f['mes']]) }}</td>
      @foreach($denoms as $d)
        <td class="right">{{ $f['celdas'][$d->id] ? number_format($f['celdas'][$d->id]) : '' }}</td>
      @endforeach
      <td class="right">{{ number_format($f['total']) }}</td>
      <td class="right">{{ number_format($f['monto'], 2) }}</td>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td><strong>TOTAL</strong></td>
      @foreach($denoms as $d)
        <td class="right"><strong>{{ number_format($tablaReal['total_col'][$d->id]) }}</strong></td>
      @endforeach
      <td class="right"><strong>{{ number_format($tablaReal['total']) }}</strong></td>
      <td class="right"><strong>{{ number_format($tablaReal['total_monto'], 2) }}</strong></td>
    </tr>
  </tfoot>
</table>

<br>
<p class="seccion">NULAS</p>
<table class="datos">
  <thead>
    <tr>
      <th style="width:12%">MES</th>
      @foreach($denoms as $d)
        <th class="right">{{ $d->descripcion ?: number_format($d->valor, 2) }}</th>
      @endforeach
      <th class="right">TOTAL</th>
    </tr>
  </thead>
  <tbody>
    @foreach($tablaNulas['filas'] as $i => $f)
    <tr class="{{ $i % 2 == 1 ? 'alt' : '' }}">
      <td>{{ strtoupper($meses[$f['mes']]) }}</td>
      @foreach($denoms as $d)
        <td class="right">{{ $f['celdas'][$d->id] ? number_format($f['celdas'][$d->id]) : '' }}</td>
      @endforeach
      <td class="right">{{ number_format($f['total']) }}</td>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td><strong>TOTAL</strong></td>
      @foreach($denoms as $d)
        <td class="right"><strong>{{ number_format($tablaNulas['total_col'][$d->id]) }}</strong></td>
      @endforeach
      <td class="right"><strong>{{ number_format($tablaNulas['total']) }}</strong></td>
    </tr>
  </tfoot>
</table>

@endif

<br>
<p style="font-size:9px; text-align:right;">Generado el {{ now()->format('d/m/Y H:i') }}</p>

</body>
</html>
