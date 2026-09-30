<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Tarifa de una denominacion en un distrito: rotulo, costo y venta para su reporte mensual
class DistritoDenominacion extends Model
{
    protected $table    = 'distrito_denominacion';
    protected $fillable = ['distrito_id', 'denominacion_id', 'descripcion', 'precio_costo', 'precio_venta',
                           'orden', 'descripcion_nulas', 'orden_nulas', 'nulas_con_venta'];
    protected $casts    = ['nulas_con_venta' => 'boolean'];

    public function distrito()
    {
        return $this->belongsTo(Distrito::class);
    }

    public function denominacion()
    {
        return $this->belongsTo(Denominacion::class);
    }
}
