<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Rotulos del reporte de realizaciones (hoja "ESPECIES MUNICIPALES REALIZADAS"), que no siempre son los del control de saldos
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distrito_tipo_especie', function (Blueprint $table) {
            $table->string('titulo_realizaciones', 150)->nullable()->after('titulo_reporte');
        });

        Schema::table('distrito_denominacion', function (Blueprint $table) {
            $table->string('descripcion_realizaciones', 150)->nullable()->after('nulas_con_venta');
            $table->unsignedSmallInteger('orden_realizaciones')->nullable()->after('descripcion_realizaciones');
            // fila propia para las nulas; sin ella las nulas se suman a la fila de la denominacion
            $table->string('nulas_realizaciones', 150)->nullable()->after('orden_realizaciones');
            $table->unsignedSmallInteger('orden_nulas_realizaciones')->nullable()->after('nulas_realizaciones');
        });
    }

    public function down(): void
    {
        Schema::table('distrito_tipo_especie', function (Blueprint $table) {
            $table->dropColumn('titulo_realizaciones');
        });

        Schema::table('distrito_denominacion', function (Blueprint $table) {
            $table->dropColumn(['descripcion_realizaciones', 'orden_realizaciones', 'nulas_realizaciones', 'orden_nulas_realizaciones']);
        });
    }
};
