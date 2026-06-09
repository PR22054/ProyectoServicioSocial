<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<style>
  body      { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #2B5880; }
  p         { text-align: justify; margin: 4px 0; }
  .ht       { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
  .ht td    { border: 1px solid #000; padding: 5px; vertical-align: middle; }
  .titulo   { text-align: center; font-weight: bold; margin: 4px 0; }
  .label    { font-weight: bold; }
  .periodo  { margin: 10px 0 16px 0; }
  .tabla    { width: 100%; border-collapse: collapse; margin-top: 16px; }
  .tabla th { background-color: #2B5880; color: #ffffff; padding: 6px 8px; border: 1px solid #1a3d5c; text-align: left; }
  .tabla td { padding: 5px 8px; border: 1px solid #2B5880; }
  .tabla tr:nth-child(even) td { background-color: #eaf1f8; }
  .centro   { text-align: center; }
</style>
</head>
<body>

  {{-- encabezado: col1 logo, col2 titulo (columnas 2,3,4 combinadas) --}}
  <table class="ht">
    <tr>
      <td style="width:30%; text-align:center;">
        <img src="{{ public_path('images/encabezado-constancia.png') }}" style="width:45%; max-height:50px;">
      </td>
      <td style="text-align:center; font-weight:bold; font-size:14px; line-height:1.6;">
        REPORTE DE CONSULTAS
      </td>
    </tr>
  </table>

  <p class="titulo">ALCALDIA MUNICIPAL DE SANTA ANA NORTE</p>
  <p class="titulo">NIT: 0214-010524-101-5</p>
  <br>

  {{-- informacion del periodo consultado --}}
  <div class="periodo">
    <p class="label">Fecha de periodo</p>
    <p>Desde: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}</p>
    <p>Hasta: {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</p>
  </div>

  {{-- tabla de NIT/DUI agrupados con conteo de consultas --}}
  <table class="tabla">
    <thead>
      <tr>
        <th class="centro" style="width:8%;">#</th>
        <th style="width:28%;">NIT / DUI</th>
        <th style="width:44%;">Nombre</th>
        <th class="centro" style="width:20%;">Consultas</th>
      </tr>
    </thead>
    <tbody>
      @foreach($datos as $i => $fila)
      <tr>
        <td class="centro">{{ $i + 1 }}</td>
        <td>{{ $fila->nitdui }}</td>
        <td>{{ $fila->nombre ?? '—' }}</td>
        <td class="centro">{{ $fila->total_consultas }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  {{-- fecha y hora exacta en que se genero este reporte --}}
  <p style="margin-top:16px;"><span class="label">Fecha y hora de reporte:</span> {{ $generadoEn }}</p>

</body>
</html>
