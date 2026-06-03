<?php

//crea la tabla consultas para registrar cada busqueda de NIT/DUI realizada en retencion
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultas', function (Blueprint $table) {
            $table->id();
            $table->string('nitdui');
            $table->string('nombre')->nullable();
            $table->timestamp('buscado_en');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultas');
    }
};
