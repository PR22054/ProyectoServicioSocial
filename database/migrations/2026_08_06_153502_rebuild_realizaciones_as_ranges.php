<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //RECREA LA TABLA realizaciones CON ESTRUCTURA DE RANGO (numero_inicio, numero_fin, cantidad)
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('realizaciones');
        Schema::enableForeignKeyConstraints();

        Schema::create('realizaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_especie_id')->constrained('tipo_especies');
            $table->foreignId('denominacion_id')->constrained('denominaciones');
            $table->foreignId('distrito_id')->constrained('distritos');
            $table->unsignedInteger('numero_inicio');
            $table->unsignedInteger('numero_fin');
            $table->unsignedInteger('cantidad');
            $table->date('fecha');
            $table->string('nombre_contribuyente', 200)->nullable();
            $table->decimal('monto_cobrado', 12, 2);
            $table->foreignId('usuario_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('realizaciones');
    }
};
