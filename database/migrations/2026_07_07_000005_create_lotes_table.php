<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compra_id')->constrained('compras');
            $table->foreignId('tipo_especie_id')->constrained('tipo_especies');
            $table->foreignId('denominacion_id')->constrained('denominaciones');
            $table->string('serie', 5)->nullable();
            $table->unsignedInteger('cantidad_total');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lotes');
    }
};
