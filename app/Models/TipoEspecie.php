<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//modelo de tipo de especie (fondo vialidad, tiquetes, formulas...)
class TipoEspecie extends Model
{
    protected $table    = 'tipo_especies';
    protected $fillable = ['nombre', 'descripcion', 'activo', 'unidades', 'orden'];
    protected $casts    = ['unidades' => 'boolean'];

    //denominaciones del tipo
    public function denominaciones()
    {
        return $this->hasMany(Denominacion::class);
    }
}
