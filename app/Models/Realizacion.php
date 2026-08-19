<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Realizacion extends Model
{
    //MODELO DE REALIZACION - documenta la entrega de especies a contribuyentes por rango de numeros
    protected $table    = 'realizaciones';
    protected $fillable = [
        'tipo_especie_id', 'denominacion_id', 'distrito_id',
        'numero_inicio', 'numero_fin', 'cantidad',
        'fecha', 'nombre_contribuyente', 'monto_cobrado', 'usuario_id',
    ];
    protected $casts = ['fecha' => 'date'];

    public function tipoEspecie()  { return $this->belongsTo(TipoEspecie::class); }
    public function denominacion() { return $this->belongsTo(Denominacion::class); }
    public function distrito()     { return $this->belongsTo(Distrito::class); }
    public function usuario()      { return $this->belongsTo(\App\Models\User::class, 'usuario_id'); }
}
