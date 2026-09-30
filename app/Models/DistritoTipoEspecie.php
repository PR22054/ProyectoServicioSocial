<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Titulos y orden de un tipo de especie en los libros y reportes de un distrito
class DistritoTipoEspecie extends Model
{
    protected $table    = 'distrito_tipo_especie';
    protected $fillable = ['distrito_id', 'tipo_especie_id', 'titulo_libro', 'titulo_reporte', 'orden', 'nulas_en_realizaciones'];
    protected $casts    = ['nulas_en_realizaciones' => 'boolean'];

    public function distrito()
    {
        return $this->belongsTo(Distrito::class);
    }

    public function tipoEspecie()
    {
        return $this->belongsTo(TipoEspecie::class);
    }
}
