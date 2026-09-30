<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Denominacion extends Model
{
    protected $table    = 'denominaciones';
    protected $fillable = ['tipo_especie_id', 'descripcion', 'valor', 'precio_costo', 'precio_venta', 'activo'];

    public function tipoEspecie()
    {
        return $this->belongsTo(TipoEspecie::class);
    }

    public function lotes()
    {
        return $this->hasMany(Lote::class);
    }

    public function realizaciones()
    {
        return $this->hasMany(Realizacion::class);
    }

    // Etiqueta que usan los libros; si no se capturo descripcion cae al valor
    public function getEtiquetaAttribute(): string
    {
        return $this->descripcion ?: 'DE $' . number_format($this->valor, 2);
    }
}
