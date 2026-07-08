<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lote_rangos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('lotes');
            $table->unsignedInteger('numero_inicio');
            $table->unsignedInteger('numero_fin');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lote_rangos');
    }
};
