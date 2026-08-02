<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nula extends Model
{
    protected $table    = 'nulas';
    protected $fillable = [
        'traslado_detalle_id', 'distrito_id',
        'numero_inicio', 'numero_fin',
        'fecha', 'motivo', 'usuario_id',
    ];
    protected $casts = ['fecha' => 'date'];

    public function trasladoDetalle()
    {
        return $this->belongsTo(TrasladoDetalle::class);
    }

    public function distrito()
    {
        return $this->belongsTo(Distrito::class);
    }

    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'usuario_id');
    }

    public function getCantidadAttribute(): int
    {
        return $this->numero_fin - $this->numero_inicio + 1;
    }
}
