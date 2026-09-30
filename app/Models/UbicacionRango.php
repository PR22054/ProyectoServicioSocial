<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Desde "fecha", el rango queda asignado a la caja; no mueve inventario
class UbicacionRango extends Model
{
    protected $table    = 'ubicacion_rangos';
    protected $fillable = ['ubicacion_id', 'tipo_especie_id', 'serie', 'numero_inicio', 'numero_fin', 'fecha', 'usuario_id'];
    protected $casts    = ['fecha' => 'date'];

    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class);
    }

    public function tipoEspecie()
    {
        return $this->belongsTo(TipoEspecie::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
