<?php

namespace App\Models;

//modelo para la tabla consultas, registra cada busqueda de NIT/DUI en retencion
use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    public $timestamps = false;

    protected $fillable = ['nitdui', 'nombre', 'buscado_en'];

    protected $casts = ['buscado_en' => 'datetime'];
}
