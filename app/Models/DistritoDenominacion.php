<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//tarifa de una denominacion en un distrito: rotulo, costo y venta para su reporte mensual
class DistritoDenominacion extends Model
{
    protected $table    = 'distrito_denominacion';
    protected $fillable = ['distrito_id', 'denominacion_id', 'descripcion', 'precio_costo', 'precio_venta',
                           'orden', 'descripcion_nulas', 'orden_nulas', 'nulas_con_venta',
                           'descripcion_realizaciones', 'orden_realizaciones', 'nulas_realizaciones', 'orden_nulas_realizaciones'];
    protected $casts    = ['nulas_con_venta' => 'boolean'];

    //distrito de la tarifa
    public function distrito()
    {
        return $this->belongsTo(Distrito::class);
    }

    //denominacion de la tarifa
    public function denominacion()
    {
        return $this->belongsTo(Denominacion::class);
    }
}
