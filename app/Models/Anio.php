<?php

namespace App\Models;

//modelo para tabla anios solo maneja el campo anio como valor unico
use Illuminate\Database\Eloquent\Model;

class Anio extends Model
{
    protected $fillable = ['anio'];
}
