<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//titulos y orden de un tipo de especie en los libros y reportes de un distrito
class DistritoTipoEspecie extends Model
{
    protected $table    = 'distrito_tipo_especie';
    protected $fillable = ['distrito_id', 'tipo_especie_id', 'titulo_libro', 'titulo_reporte', 'titulo_realizaciones', 'orden', 'nulas_en_realizaciones'];
    protected $casts    = ['nulas_en_realizaciones' => 'boolean'];

    //distrito de la configuracion
    public function distrito()
    {
        return $this->belongsTo(Distrito::class);
    }

    //tipo de especie de la configuracion
    public function tipoEspecie()
    {
        return $this->belongsTo(TipoEspecie::class);
    }
}
