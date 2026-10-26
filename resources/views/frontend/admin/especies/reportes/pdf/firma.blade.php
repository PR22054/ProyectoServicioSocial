{{--PIE DE LOS REPORTES: lugar y fecha opcional ($pie), espacio y linea para la firma de la tesorera
     y la fecha de generacion (se omite con $sinFechaGeneracion).
     es una sola tabla que no se parte: si no cabe al final de la pagina pasa completa a la siguiente--}}
@php($firmante = config('especies.firmas.tesorera'))
<table style="width:100%; margin-top:10px; page-break-inside:avoid;">
  @isset($pie)
  <tr><td class="pie" style="text-align:left;">{{ $pie }}</td></tr>
  @endisset
  <tr><td style="height:90px;"></td></tr>
  <tr>
    <td align="center">
      <table style="width:45%; text-align:center; font-weight:bold;">
        <tr><td style="border-top:1px solid #000; padding-top:4px;">{{ $firmante['nombre'] }}<br>{{ $firmante['cargo'] }}</td></tr>
      </table>
    </td>
  </tr>
  @unless($sinFechaGeneracion ?? false)
  <tr><td style="font-size:8px; text-align:right; padding-top:10px;">Generado el {{ now()->format('d/m/Y H:i') }}</td></tr>
  @endunless
</table>
