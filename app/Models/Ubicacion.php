<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Caja o punto de cobro dentro de un distrito
class Ubicacion extends Model
{
    protected $table    = 'ubicaciones';
    protected $fillable = ['distrito_id', 'nombre', 'orden'];

    public function distrito()
    {
        return $this->belongsTo(Distrito::class);
    }

    public function rangos()
    {
        return $this->hasMany(UbicacionRango::class);
    }
}
