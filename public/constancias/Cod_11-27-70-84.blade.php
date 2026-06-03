<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<style>
  body       { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #2B5880; }
  p          { text-align: justify; }
  .ht        { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
  .ht td     { border: 1px solid #000; padding: 5px; vertical-align: middle; }
  .titulo    { text-align: center; font-weight: bold; margin: 4px 0; }
  .bloque    { margin: 8px 0; }
  .label     { font-weight: bold; }
</style>
</head>
<body>

  {{-- encabezado identico al de Cod_01-60-80-81 --}}
  <table class="ht">
    <tr>
      <td rowspan="3" style="width:30%; text-align:center;">
        <img src="{{ public_path('images/encabezado-constancia.png') }}" style="width:45%; max-height:50px;">
      </td>
      <td rowspan="3" style="width:30%; text-align:center; font-weight:bold; font-size:14px; line-height:1.6;">
        CONSTANCIA<br>DE RETENCION
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
  <p>LA INFRASCRITA AGENTE DE RETENCION EN CUMPLIMIENTO DEL ART. 145 DEL CODIGO TRIBUTARIO HACE CONSTAR QUE:</p>
  <br>

  {{-- datos del empleado: dos puntos tras la etiqueta, valor en negrita a la par --}}
  <p class="bloque"><span class="label">NOMBRE:</span>&nbsp;&nbsp;<span class="label">{{ $nombre }}</span></p>
  <p class="bloque"><span class="label">NIT/DUI:</span>&nbsp;&nbsp;<span class="label">{{ $nit }}</span></p>
  <p class="bloque"><span class="label">CODIGO DE RETENCION:</span>&nbsp;&nbsp;<span class="label">{{ $codigo }}</span></p>

  <br>
  <p>DEVENGO LOS SIGUIENTES INGRESOS EN EL AÑO {{ $anio }}:</p>
  <br>

  {{-- montos con exactamente 3 puntos entre etiqueta y valor --}}
  <p class="label">INGRESOS:</p>
  <p>MONTO DEVENGADO ...........................................................${{ number_format($monto_devengado, 2) }}</p>
  <br>
  <p class="label">DEDUCCIONES:</p>
  <br>
  <p class="label">IMPUESTO RETENIDO ..............................................<u>${{ number_format($impuesto_retenido, 2) }}</u></p>
  <br>

  <p>Y PARA LOS EFECTOS DE SU DECLARACION DE IMPUESTO SOBRE LA RENTA, SE EXTIENDE LA PRESENTE EN LA UNIDAD DE TESORERIA DE LA ALCALDIA MUNICIPAL DE SANTA ANA NORTE, DISTRITO DE METAPAN A LOS 19 DIAS DEL MES DE FEBRERO DE 2026.</p>

  {{-- firma centrada en la pagina, sello a la par hacia la derecha --}}
  <table style="width:100%; margin-top:50px; border-collapse:collapse;">
    <tr>
      <td style="width:30%;"></td>
      <td style="width:35%; text-align:center; vertical-align:bottom; padding:4px;">
        <img src="{{ public_path('images/Firma.png') }}" style="max-height:100px; max-width:210px;">
      </td>
      <td style="width:35%; text-align:left; vertical-align:bottom; padding:4px 4px 4px 24px;">
        <img src="{{ public_path('images/Sello.png') }}" style="max-height:120px; max-width:120px;">
      </td>
    </tr>
    <tr>
      <td colspan="3" style="text-align:center; padding-top:6px;">
        <strong>LIC. DELMY MARILIN MURILLOS JERONIMO</strong><br>
        <strong>TESORERA MUNICIPAL</strong>
      </td>
    </tr>
  </table>

</body>
</html>
