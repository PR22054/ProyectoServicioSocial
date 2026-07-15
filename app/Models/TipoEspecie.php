<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoEspecie extends Model
{
    protected $table    = 'tipo_especies';
    protected $fillable = ['nombre', 'descripcion', 'activo'];

    public function denominaciones()
    {
        return $this->hasMany(Denominacion::class);
    }
}
