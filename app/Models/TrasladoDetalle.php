<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrasladoDetalle extends Model
{
    public $timestamps  = false;
    protected $fillable = ['traslado_id', 'lote_id', 'numero_inicio', 'numero_fin', 'cantidad'];

    public function traslado()
    {
        return $this->belongsTo(Traslado::class);
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }
}
