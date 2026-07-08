<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('traslado_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('traslado_id')->constrained('traslados');
            $table->foreignId('lote_id')->constrained('lotes');
            $table->unsignedInteger('numero_inicio');
            $table->unsignedInteger('numero_fin');
            $table->unsignedInteger('cantidad');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('traslado_detalles');
    }
};
