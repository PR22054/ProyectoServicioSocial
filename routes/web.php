<?php

use Illuminate\Support\Facades\Route;

//ruta por defecto
Route::get('/', function () {
    return view('welcome');
});
