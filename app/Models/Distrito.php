<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//modelo de distrito con su configuracion de libros y reportes
class Distrito extends Model
{
    protected $fillable = ['nombre', 'codigo', 'activo', 'nombre_oficial', 'lugar_firma', 'saldo_por_valor'];
    protected $casts    = ['saldo_por_valor' => 'boolean'];

    //traslados que recibe el distrito
    public function traslados()
    {
        return $this->hasMany(Traslado::class);
    }

    //configuracion de cada tipo de especie en el distrito (titulos, orden, nulas)
    public function tiposConfig()
    {
        return $this->hasMany(DistritoTipoEspecie::class);
    }

    //tarifas del distrito por denominacion
    public function tarifas()
    {
        return $this->hasMany(DistritoDenominacion::class);
    }


    //como aparece en los encabezados de libros y reportes (ej. "DISTRITO DE METAPAN")
    public function getNombreReporteAttribute(): string
    {
        return $this->nombre_oficial ?: mb_strtoupper($this->nombre);
    }
}
