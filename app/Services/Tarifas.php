<?php

namespace App\Services;

use App\Models\Denominacion;
use App\Models\DistritoDenominacion;

//tarifa de una denominacion en un distrito. cada distrito tiene sus propios costos, precios
//de venta y rotulos; si no tiene tarifa propia se usan los de la denominacion.
class Tarifas
{
    //descripcion, costo y precio de venta de una denominacion en el distrito
    public static function de(int $distritoId, Denominacion $den): array
    {
        $t = DistritoDenominacion::where('distrito_id', $distritoId)->where('denominacion_id', $den->id)->first();

        return [
            'descripcion'  => $t?->descripcion ?: $den->etiqueta,
            'precio_costo' => $t?->precio_costo ?? $den->precio_costo,
            //en la tarifa, null es una decision del distrito: esa especie no se vende ahi
            'precio_venta' => $t ? $t->precio_venta : $den->precio_venta,
        ];
    }

    //precio de venta de la denominacion en el distrito (null si no se vende)
    public static function precioVenta(int $distritoId, Denominacion $den): ?float
    {
        $p = self::de($distritoId, $den)['precio_venta'];
        return $p === null ? null : (float) $p;
    }
}
