<?php

namespace App\Services;

use App\Models\Denominacion;
use App\Models\DistritoDenominacion;

/**
 * Tarifa de una denominacion en un distrito. Cada distrito tiene sus propios costos, precios
 * de venta y rotulos; si no tiene tarifa propia se usan los de la denominacion.
 */
class Tarifas
{
    public static function de(int $distritoId, Denominacion $den): array
    {
        $t = DistritoDenominacion::where('distrito_id', $distritoId)->where('denominacion_id', $den->id)->first();

        return [
            'descripcion'  => $t?->descripcion ?: $den->etiqueta,
            'precio_costo' => $t?->precio_costo ?? $den->precio_costo,
            // en la tarifa, null es una decision del distrito: esa especie no se vende ahi
            'precio_venta' => $t ? $t->precio_venta : $den->precio_venta,
        ];
    }

    public static function precioVenta(int $distritoId, Denominacion $den): ?float
    {
        $p = self::de($distritoId, $den)['precio_venta'];
        return $p === null ? null : (float) $p;
    }
}
