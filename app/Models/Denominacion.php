<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Denominacion extends Model
{
    protected $table    = 'denominaciones';
    protected $fillable = ['tipo_especie_id', 'valor', 'activo'];

    public function tipoEspecie()
    {
        return $this->belongsTo(TipoEspecie::class);
    }
}
