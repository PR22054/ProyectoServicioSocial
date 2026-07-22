<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    protected $fillable = ['compra_id', 'tipo_especie_id', 'denominacion_id', 'serie', 'cantidad_total'];

    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }

    public function tipoEspecie()
    {
        return $this->belongsTo(TipoEspecie::class);
    }

    public function denominacion()
    {
        return $this->belongsTo(Denominacion::class);
    }

    public function rangos()
    {
        return $this->hasMany(LoteRango::class);
    }
}
