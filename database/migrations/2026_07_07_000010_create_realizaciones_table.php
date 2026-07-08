<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('realizaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('numero_serie')->unique();
            $table->foreignId('tipo_especie_id')->constrained('tipo_especies');
            $table->foreignId('denominacion_id')->constrained('denominaciones');
            $table->foreignId('distrito_id')->constrained('distritos');
            $table->date('fecha');
            $table->string('nombre_contribuyente', 200)->nullable();
            $table->decimal('monto_cobrado', 10, 2);
            $table->foreignId('usuario_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('realizaciones');
    }
};
