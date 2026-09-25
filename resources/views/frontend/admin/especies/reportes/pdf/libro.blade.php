{{-- PDF: LIBRO DE ESPECIES MUNICIPALES - mayor mensual valuado, con rangos y serie --}}
@php $tituloReporte = 'Libro de Especies Municipales'; @endphp
@include('frontend.admin.especies.reportes.pdf._header')

@php
  $valorNula   = fn($n) => (float) ($n->trasladoDetalle->lote->denominacion->valor ?? 0);
  $cantNula    = fn($n) => $n->numero_fin - $n->numero_inicio + 1;

  $trasCant    = $trasladosMes->sum('cantidad');
  $trasMonto   = $trasladosMes->sum(fn($d) => $d->cantidad * (float) ($d->lote->denominacion->valor ?? 0));
  $salCant     = $salidasMes->sum('cantidad');
  $salMonto    = $salidasMes->sum(fn($d) => $d->cantidad * (float) ($d->lote->denominacion->valor ?? 0));
  $realCant    = $realizacionesMes->sum('cantidad');
  $realMonto   = $realizacionesMes->sum('monto_cobrado');
  $nulaCant    = $nulasMes->sum($cantNula);
  $nulaMonto   = $nulasMes->sum(fn($n) => $cantNula($n) * $valorNula($n));

  $primerDia   = sprintf('01/%02d/%d', $mes, $anio);
  $ultimoDia   = \Carbon\Carbon::create($anio, $mes, 1)->endOfMonth()->format('d/m/Y');
@endphp

<p class="label">
  {{ strtoupper($tipo->nombre) }}
  &nbsp;|&nbsp; Distrito: {{ $distrito->nombre }} ({{ $distrito->codigo }})
  &nbsp;|&nbsp; {{ strtoupper($nombreMes) }} {{ $anio }}
  @if($denomFiltro) &nbsp;|&nbsp; Denominación: ${{ number_format($denomFiltro->valor, 2) }} @endif
</p>
<br>

<table class="datos">
  <thead>
    <tr>
      <th style="width:11%">FECHA</th>
      <th class="right" style="width:9%">CANTIDAD</th>
      <th class="right" style="width:8%">VALOR</th>
      <th class="right" style="width:11%">DEL</th>
      <th class="right" style="width:11%">AL</th>
      <th class="center" style="width:7%">SERIE</th>
      <th class="right" style="width:14%">EXISTENCIA</th>
      <th class="right" style="width:14%">REALIZACION</th>
      <th class="right" style="width:14%">SALDO</th>
    </tr>
  </thead>
  <tbody>

    {{-- SALDO ANTERIOR --}}
    <tr>
      <td>{{ $primerDia }}</td>
      <td colspan="5"><strong>SALDO ANTERIOR</strong></td>
      <td class="right">{{ number_format($saldoInicioMonto, 2) }}</td>
      <td></td>
      <td class="right"><strong>{{ number_format($saldoInicioMonto, 2) }}</strong></td>
    </tr>
    @foreach($saldoInicioDet as $row)
      @foreach($row['intervalos'] as $k => $iv)
      <tr>
        <td></td>
        <td class="right">{{ number_format($iv[1] - $iv[0] + 1) }}</td>
        <td class="right">{{ number_format($row['valor'], 2) }}</td>
        <td class="right">{{ number_format($iv[0]) }}</td>
        <td class="right">{{ number_format($iv[1]) }}</td>
        <td class="center">{{ $row['lote']->serie ?: '—' }}</td>
        <td class="right">{{ number_format(($iv[1] - $iv[0] + 1) * $row['valor'], 2) }}</td>
        <td></td>
        <td></td>
      </tr>
      @endforeach
    @endforeach

    {{-- COMPRAS: en un distrito las especies entran por traslado, no por factura directa --}}
    <tr>
      <td></td>
      <td colspan="5"><strong>POR COMPRA AL M.H. FACTURA N°</strong></td>
      <td></td>
      <td></td>
      <td class="right">0.00</td>
    </tr>

    {{-- TRASLADOS RECIBIDOS --}}
    <tr>
      <td></td>
      <td colspan="5"><strong>POR TRASLADOS RECIBIDOS</strong></td>
      <td class="right">{{ number_format($trasMonto, 2) }}</td>
      <td></td>
      <td class="right"><strong>{{ number_format($trasMonto, 2) }}</strong></td>
    </tr>
    @foreach($trasladosMes as $d)
    <tr>
      <td>{{ $d->traslado->fecha->format('d/m/Y') }}</td>
      <td class="right">{{ number_format($d->cantidad) }}</td>
      <td class="right">{{ number_format($d->lote->denominacion->valor ?? 0, 2) }}</td>
      <td class="right">{{ number_format($d->numero_inicio) }}</td>
      <td class="right">{{ number_format($d->numero_fin) }}</td>
      <td class="center">{{ $d->lote->serie ?: '—' }}</td>
      <td class="right">{{ number_format($d->cantidad * (float) ($d->lote->denominacion->valor ?? 0), 2) }}</td>
      <td></td>
      <td></td>
    </tr>
    @endforeach

    {{-- REALIZACIONES --}}
    <tr>
      <td>{{ $ultimoDia }}</td>
      <td colspan="5"><strong>POR REALIZACIONES EN EL MES</strong></td>
      <td></td>
      <td class="right">{{ number_format($realMonto, 2) }}</td>
      <td class="right"><strong>{{ number_format($realMonto, 2) }}</strong></td>
    </tr>
    @foreach($realizacionesMes as $r)
    <tr>
      <td>{{ $r->fecha->format('d/m/Y') }}</td>
      <td class="right">{{ number_format($r->cantidad) }}</td>
      <td class="right">{{ number_format($r->denominacion->valor ?? 0, 2) }}</td>
      <td class="right">{{ number_format($r->numero_inicio) }}</td>
      <td class="right">{{ number_format($r->numero_fin) }}</td>
      <td class="center">—</td>
      <td class="right">{{ number_format($r->monto_cobrado, 2) }}</td>
      <td class="right">{{ number_format($r->monto_cobrado, 2) }}</td>
      <td></td>
    </tr>
    @endforeach

    {{-- NULAS --}}
    <tr>
      <td></td>
      <td colspan="5"><strong>NULAS</strong></td>
      <td></td>
      <td class="right">{{ number_format($nulaMonto, 2) }}</td>
      <td class="right"><strong>{{ number_format($nulaMonto, 2) }}</strong></td>
    </tr>
    @foreach($nulasMes as $n)
    <tr>
      <td>{{ $n->fecha->format('d/m/Y') }}</td>
      <td class="right">{{ number_format($cantNula($n)) }}</td>
      <td class="right">{{ number_format($valorNula($n), 2) }}</td>
      <td class="right">{{ number_format($n->numero_inicio) }}</td>
      <td class="right">{{ number_format($n->numero_fin) }}</td>
      <td class="center">{{ $n->trasladoDetalle->lote->serie ?: '—' }}</td>
      <td class="right">{{ number_format($cantNula($n) * $valorNula($n), 2) }}</td>
      <td class="right">{{ number_format($cantNula($n) * $valorNula($n), 2) }}</td>
      <td></td>
    </tr>
    @endforeach

    {{-- SALIDAS POR TRASLADO --}}
    <tr>
      <td></td>
      <td colspan="5"><strong>POR TRASLADOS ENVIADOS</strong></td>
      <td></td>
      <td class="right">{{ number_format($salMonto, 2) }}</td>
      <td class="right"><strong>{{ number_format($salMonto, 2) }}</strong></td>
    </tr>
    @foreach($salidasMes as $d)
    <tr>
      <td>{{ $d->traslado->fecha->format('d/m/Y') }}</td>
      <td class="right">{{ number_format($d->cantidad) }}</td>
      <td class="right">{{ number_format($d->lote->denominacion->valor ?? 0, 2) }}</td>
      <td class="right">{{ number_format($d->numero_inicio) }}</td>
      <td class="right">{{ number_format($d->numero_fin) }}</td>
      <td class="center">{{ $d->lote->serie ?: '—' }}</td>
      <td class="right">{{ number_format($d->cantidad * (float) ($d->lote->denominacion->valor ?? 0), 2) }}</td>
      <td class="right">{{ number_format($d->cantidad * (float) ($d->lote->denominacion->valor ?? 0), 2) }}</td>
      <td></td>
    </tr>
    @endforeach

    {{-- SALDO A NUEVA CUENTA --}}
    <tr>
      <td>{{ $ultimoDia }}</td>
      <td colspan="5"><strong>POR SALDO A NUEVA CUENTA</strong></td>
      <td class="right">{{ number_format($saldoFinalMonto, 2) }}</td>
      <td></td>
      <td class="right"><strong>{{ number_format($saldoFinalMonto, 2) }}</strong></td>
    </tr>
    @foreach($saldoFinalDet as $row)
      @foreach($row['intervalos'] as $iv)
      <tr>
        <td></td>
        <td class="right">{{ number_format($iv[1] - $iv[0] + 1) }}</td>
        <td class="right">{{ number_format($row['valor'], 2) }}</td>
        <td class="right">{{ number_format($iv[0]) }}</td>
        <td class="right">{{ number_format($iv[1]) }}</td>
        <td class="center">{{ $row['lote']->serie ?: '—' }}</td>
        <td class="right">{{ number_format(($iv[1] - $iv[0] + 1) * $row['valor'], 2) }}</td>
        <td></td>
        <td></td>
      </tr>
      @endforeach
    @endforeach

  </tbody>
</table>

<br>
<p class="sub">
  Documentos: saldo anterior {{ number_format($saldoInicio) }}
  + recibidos {{ number_format($trasCant) }}
  &minus; realizados {{ number_format($realCant) }}
  &minus; nulas {{ number_format($nulaCant) }}
  &minus; enviados {{ number_format($salCant) }}
  = <strong>{{ number_format($saldoFinal) }}</strong>.
  Montos valuados al precio de venta de cada denominación.
</p>

<br>
<p style="font-size:9px; text-align:right;">Generado el {{ now()->format('d/m/Y H:i') }}</p>

</body>
</html>
