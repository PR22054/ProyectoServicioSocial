{{-- PDF: LIBRO DE ESPECIES MUNICIPALES (TESO-008) - mismas secciones, rotulos y columnas que el libro de Tesoreria.
     Los montos van en EXISTENCIA (los traslados enviados en REALIZACION); el total de cada seccion en SALDO y las
     realizaciones repiten su total en REALIZACION en la primera fila. Las nulas se descuentan aparte salvo en los
     libros que las suman a las realizaciones. Los tipos que se llevan en unidades no tienen columna VALOR. --}}
@php
  $tituloReporte      = 'Libro de Especies Municipales';
  $codigoFormulario   = config('especies.libro.codigo');
  $versionFormulario  = config('especies.libro.version');
  $vigenciaFormulario = config('especies.libro.vigencia');
  $encabezadoCaja     = 'FORMULARIO LIBRO DE ESPECIES MUNICIPALES';
  $sinNit             = true;

  $columnas = $unidades ? 4 : 5;
  $num      = fn($v) => $unidades ? number_format($v) : number_format($v, 2);
  $fecha    = fn($f) => $f ? $f->format('d/m/Y') : '';
@endphp
@include('frontend.admin.especies.reportes.pdf._header')

<p class="titulo">UNIDAD DE TESORERÍA</p>
<p class="titulo">LIBRO DE ESPECIES MUNICIPALES</p>
<p class="titulo">ALCALDIA MUNICIPAL DE SANTA ANA NORTE</p>
<p class="titulo">{{ $titulo }}</p>
<p class="titulo">{{ mb_strtoupper($nombreMes) }} {{ $anio }}</p>
<p class="titulo">{{ $distrito->nombre_reporte }}</p>
@if($denomFiltro)
  <p class="center sub">Solo la denominación {{ $denomFiltro->etiqueta }}</p>
@endif

<table class="datos">
  <thead>
    <tr>
      <th style="width:12%">FECHA</th>
      <th colspan="{{ $columnas }}">CONCEPTO</th>
      <th class="right" style="width:13%">EXISTENCIA</th>
      <th class="right" style="width:13%">REALIZACION</th>
      <th class="right" style="width:13%">SALDO</th>
    </tr>
  </thead>
  <tbody>
  @foreach($secciones as $s)
    @if($s['tipo'] === 'nulas' && $s['filas'])
      {{-- el libro asienta las nulas con el rotulo en la primera fila y los totales a la par --}}
      @foreach($s['filas'] as $k => $r)
      <tr>
        <td>{{ $k === 0 ? 'NULAS' : ($r['caja'] ?? '') }}</td>
        <td class="right">{{ number_format($r['cantidad']) }}</td>
        @unless($unidades)<td class="right">{{ number_format($r['valor'], 2) }}</td>@endunless
        <td class="right">{{ number_format($r['del']) }}</td>
        <td class="right">{{ number_format($r['al']) }}</td>
        <td class="center">{{ $r['serie'] ? '"' . $r['serie'] . '"' : '-' }}</td>
        <td class="right">{{ $num($r['monto']) }}</td>
        <td class="right">{{ $k === 0 ? $num($s['total']) : '' }}</td>
        <td class="right">{{ $k === 0 && !$nulasJuntas ? $num($s['total']) : '' }}</td>
      </tr>
      @endforeach
      @continue
    @endif

    <tr>
      <td>{{ $fecha($s['fecha']) }}</td>
      <td colspan="{{ $columnas }}"><strong>{{ $s['concepto'] }}</strong>@if($s['factura']) {{ $s['factura'] }}@endif</td>
      <td class="right">{{ (in_array($s['tipo'], ['compra', 'entrada']) && $s['filas']) || $s['tipo'] === 'final' ? $num($s['total']) : '' }}</td>
      <td class="right">{{ $s['tipo'] === 'salida' && $s['filas'] ? $num($s['total']) : '' }}</td>
      <td class="right"><strong>{{ $num($s['total']) }}</strong></td>
    </tr>

    @if($s['tipo'] === 'inicio')
    <tr>
      <td></td>
      <td class="center"><strong>CANTIDAD</strong></td>
      @unless($unidades)<td class="center"><strong>VALOR</strong></td>@endunless
      <td class="center"><strong>DEL</strong></td>
      <td class="center"><strong>AL</strong></td>
      <td class="center"><strong>SERIE</strong></td>
      <td class="right">{{ $num($s['total']) }}</td>
      <td></td>
      <td></td>
    </tr>
    @endif

    @foreach($s['filas'] as $k => $r)
    <tr>
      <td>{{ $r['caja'] ?? '' }}</td>
      <td class="right">{{ number_format($r['cantidad']) }}</td>
      @unless($unidades)<td class="right">{{ number_format($r['valor'], 2) }}</td>@endunless
      <td class="right">{{ number_format($r['del']) }}</td>
      <td class="right">{{ number_format($r['al']) }}</td>
      <td class="center">{{ $r['serie'] ? '"' . $r['serie'] . '"' : '-' }}</td>
      @if($s['tipo'] === 'salida')
        <td></td>
        <td class="right">{{ $num($r['monto']) }}</td>
      @else
        <td class="right">{{ $num($r['monto']) }}</td>
        <td class="right">{{ $k === 0 && $s['tipo'] === 'realizacion' ? $num($s['total']) : '' }}</td>
      @endif
      <td></td>
    </tr>
    @endforeach
  @endforeach
  </tbody>
</table>

<p class="pie">{{ $pie }}</p>
<table class="firmas">
  <tr>
    <td>{{ $firmas['tesorera']['nombre'] }}<br>{{ $firmas['tesorera']['cargo'] }}</td>
    <td>{{ $firmas['alcalde']['nombre'] }}<br>{{ $firmas['alcalde']['cargo'] }}</td>
  </tr>
</table>

<p style="font-size:8px; text-align:right; margin-top:10px;">Generado el {{ now()->format('d/m/Y H:i') }}</p>

</body>
</html>
