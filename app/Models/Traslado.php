<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Traslado extends Model
{
    protected $fillable = ['distrito_id', 'fecha', 'observaciones', 'usuario_id'];

    protected $casts = ['fecha' => 'date'];

    public function distrito()
    {
        return $this->belongsTo(Distrito::class);
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
