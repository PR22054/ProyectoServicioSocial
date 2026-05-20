<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<style>
  body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; }
  .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
  .header-table td { border: 1px solid #000; padding: 4px; font-weight: bold; }
  .center { text-align: center; }
  .titulo { text-align: center; font-weight: bold; margin: 4px 0; }
  .bloque { margin: 10px 0; }
  .label { font-weight: bold; }
  .firma { text-align: center; margin-top: 60px; font-weight: bold; }
</style>
</head>
<body>
  <table class="header-table">
    <tr>
      <td style="width:50%"></td>
      <td class="center">CONSTANCIA DE RETENCION</td>
      <td>Código:</td>
      <td>TESO-001-CONS</td>
    </tr>
    <tr><td></td><td></td><td>Versión:</td><td>001</td></tr>
    <tr><td></td><td></td><td>Fecha de vigencia:</td><td>11/11/2025</td></tr>
  </table>

  <p class="titulo">ALCALDIA MUNICIPAL DE SANTA ANA NORTE</p>
  <p class="titulo">NIT: 0214-010524-101-5</p>

  <p>LA INFRASCRITA AGENTE DE RETENCION EN CUMPLIMIENTO DEL ART. 145 DEL CODIGO TRIBUTARIO HACE CONSTAR QUE:</p>

  <p class="bloque"><span class="label">NOMBRE</span> &nbsp;&nbsp;&nbsp; {{ $nombre }}</p>
  <p class="bloque"><span class="label">NIT/DUI</span> &nbsp;&nbsp;&nbsp; {{ $nit }}</p>
  <p class="bloque"><span class="label">CODIGO DE RETENCIÓN</span> &nbsp;&nbsp;&nbsp; {{ $codigo }}</p>

  <p>DEVENGO LOS SIGUIENTES INGRESOS EN EL AÑO {{ $anio }}:</p>

  <p class="label">INGRESOS:</p>
  <p>MONTO DEVENGADO &nbsp;……………………………. $ {{ number_format($monto_devengado, 2) }}</p>

  <p class="label">DEDUCCIONES:</p>

  <p class="label">IMPUESTO RETENIDO: &nbsp;………………................... $ {{ number_format($impuesto_retenido, 2) }}</p>

  <p>Y PARA LOS EFECTOS DE SU DECLARACION DE IMPUESTO SOBRE LA RENTA, SE EXTIENDE LA PRESENTE EN LA UNIDAD DE TESORERIA DE LA ALCALDIA MUNICIPAL DE SANTA ANA NORTE, DISTRITO DE METAPAN A LOS {{ $dia }} DIAS DEL MES DE {{ $mes }} DE {{ $anio_emision }}.</p>

  <p class="firma">LIC. DELMY MARILIN MURILLOS JERONIMO</p>
  <p class="firma">TESORERA MUNICIPAL</p>
</body>
</html>