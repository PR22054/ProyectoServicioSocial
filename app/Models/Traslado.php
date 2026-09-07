<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Traslado extends Model
{
    protected $fillable = ['tipo', 'origen_distrito_id', 'distrito_id', 'fecha', 'observaciones', 'usuario_id'];

    protected $casts = ['fecha' => 'date'];

    // Distrito destino (NULL = bodega para devoluciones)
    public function distrito()
    {
        return $this->belongsTo(Distrito::class, 'distrito_id');
    }

    // Distrito origen (NULL = bodega para traslados normales)
    public function origenDistrito()
    {
        return $this->belongsTo(Distrito::class, 'origen_distrito_id');
    }

    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'usuario_id');
    }

    public function detalles()
    {
        return $this->hasMany(TrasladoDetalle::class);
    }
}
