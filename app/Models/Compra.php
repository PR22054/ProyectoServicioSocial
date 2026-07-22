<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $fillable = ['numero_factura', 'fecha', 'observaciones', 'monto_total', 'user_id'];

    protected $casts = ['fecha' => 'date'];

    public function lotes()
    {
        return $this->hasMany(Lote::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
