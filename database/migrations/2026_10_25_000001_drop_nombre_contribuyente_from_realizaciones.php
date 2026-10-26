<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

//el nombre del contribuyente ya no se registra en las realizaciones
return new class extends Migration
{
    //quita la columna
    public function up(): void
    {
        Schema::table('realizaciones', function (Blueprint $table) {
            $table->dropColumn('nombre_contribuyente');
        });
    }

    //vuelve a crear la columna vacia
    public function down(): void
    {
        Schema::table('realizaciones', function (Blueprint $table) {
            $table->string('nombre_contribuyente', 200)->nullable()->after('fecha');
        });
    }
};
