<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distrito extends Model
{
    protected $fillable = ['nombre', 'codigo', 'activo', 'nombre_oficial', 'lugar_firma', 'saldo_por_valor'];
    protected $casts    = ['saldo_por_valor' => 'boolean'];

    public function traslados()
    {
        return $this->hasMany(Traslado::class);
    }

    public function tiposConfig()
    {
        return $this->hasMany(DistritoTipoEspecie::class);
    }

    public function tarifas()
    {
        return $this->hasMany(DistritoDenominacion::class);
    }

    public function ubicaciones()
    {
        return $this->hasMany(Ubicacion::class)->orderBy('orden');
    }

    // Como aparece en los encabezados de libros y reportes (ej. "DISTRITO DE METAPÁN")
    public function getNombreReporteAttribute(): string
    {
        return $this->nombre_oficial ?: mb_strtoupper($this->nombre);
    }
}
