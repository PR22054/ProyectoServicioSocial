<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distrito extends Model
{
    protected $fillable = ['nombre', 'codigo', 'activo'];

    public function traslados()
    {
        return $this->hasMany(Traslado::class);
    }
}
