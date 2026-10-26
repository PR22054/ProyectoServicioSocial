<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//modelo de denominacion: cada valor de un tipo de especie con su precio de costo y de venta
class Denominacion extends Model
{
    protected $table    = 'denominaciones';
    protected $fillable = ['tipo_especie_id', 'descripcion', 'valor', 'precio_costo', 'precio_venta', 'activo'];

    //tipo de especie al que pertenece
    public function tipoEspecie()
    {
        return $this->belongsTo(TipoEspecie::class);
    }

    //lotes comprados de esta denominacion
    public function lotes()
    {
        return $this->hasMany(Lote::class);
    }

    //realizaciones de esta denominacion
    public function realizaciones()
    {
        return $this->hasMany(Realizacion::class);
    }

    //etiqueta que usan los libros; si no se capturo descripcion cae al valor
    public function getEtiquetaAttribute(): string
    {
        return $this->descripcion ?: 'DE $' . number_format($this->valor, 2);
    }
}
