<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoteRango extends Model
{
    public $timestamps  = false;
    protected $fillable = ['lote_id', 'numero_inicio', 'numero_fin'];

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }
}
