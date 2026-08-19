{{-- ENCABEZADO COMPARTIDO PARA TODOS LOS PDF DE REPORTES - REQUIERE LA VARIABLE $tituloReporte --}}
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<style>
  body        { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #2B5880; }
  p           { margin: 3px 0; }
  .titulo     { text-align: center; font-weight: bold; margin: 3px 0; }
  .ht         { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
  .ht td      { border: 1px solid #000; padding: 4px; vertical-align: middle; }
  .datos      { width: 100%; border-collapse: collapse; margin: 8px 0; font-size: 9px; }
  .datos th   { background: #2B5880; color: #fff; padding: 4px 3px; text-align: center; }
  .datos td   { border: 1px solid #ccc; padding: 3px 4px; vertical-align: middle; }
  .datos tr.alt td { background: #f0f5fb; }
  .datos tfoot td  { border-top: 2px solid #2B5880; font-weight: bold; background: #e8eff8; }
  .right      { text-align: right; }
  .center     { text-align: center; }
  .label      { font-weight: bold; }
  .sub        { font-size: 9px; color: #555; }
  .seccion    { margin: 8px 0 4px; font-weight: bold; font-size: 10px;
                border-bottom: 1px solid #2B5880; padding-bottom: 2px; color: #2B5880; }
  .badge-ok   { background: #d4edda; color: #155724; padding: 1px 4px; border-radius: 3px; }
  .badge-warn { background: #fff3cd; color: #856404; padding: 1px 4px; border-radius: 3px; }
</style>
</head>
<body>

<table class="ht">
  <tr>
    <td rowspan="3" style="width:28%; text-align:center;">
      <img src="{{ public_path('images/encabezado-constancia.png') }}" style="width:45%; max-height:50px;">
    </td>
    <td rowspan="3" style="width:32%; text-align:center; font-weight:bold; font-size:13px; line-height:1.6;">
      REPORTE DE<br>{{ strtoupper($tituloReporte) }}
    </td>
    <td style="font-weight:bold;">Código:</td>
    <td>TESO-001-CONS</td>
  </tr>
  <tr>
    <td style="font-weight:bold;">Versión:</td>
    <td>001</td>
  </tr>
  <tr>
    <td style="font-weight:bold;">Fecha de<br>vigencia:</td>
    <td>11/11/2025</td>
  </tr>
</table>

<p class="titulo">ALCALDIA MUNICIPAL DE SANTA ANA NORTE</p>
<p class="titulo">NIT: 0214-010524-101-5</p>
<br>
