<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

//las cajas de distrito (CAJA 1, RASTRO...) solo rotulaban el libro de metapan; se quitan del sistema
return new class extends Migration
{
    //borra las tablas de cajas
    public function up(): void
    {
        Schema::dropIfExists('ubicacion_rangos');
        Schema::dropIfExists('ubicaciones');
    }

    //vuelve a crear las tablas vacias
    public function down(): void
    {
        Schema::create('ubicaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distrito_id')->constrained('distritos');
            $table->string('nombre', 60);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
            $table->unique(['distrito_id', 'nombre']);
        });

        Schema::create('ubicacion_rangos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ubicacion_id')->constrained('ubicaciones');
            $table->foreignId('tipo_especie_id')->constrained('tipo_especies');
            $table->string('serie', 10)->nullable();
            $table->unsignedInteger('numero_inicio');
            $table->unsignedInteger('numero_fin');
            $table->date('fecha');
            $table->foreignId('usuario_id')->constrained('users');
            $table->timestamps();
            $table->index(['tipo_especie_id', 'serie', 'numero_inicio']);
        });
    }
};
