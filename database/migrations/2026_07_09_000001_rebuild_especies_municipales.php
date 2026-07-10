<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Reconstruye todas las tablas de especies municipales con el esquema corregido:
// - nulas ahora referencia traslado_detalles (no lotes) y agrega distrito_id
// - realizaciones cambia unique a (numero_serie, tipo_especie_id)
return new class extends Migration
{
    public function up(): void
    {
        // drop en orden inverso de dependencias
        Schema::disableForeignKeyConstraints();
        foreach (['realizaciones', 'nulas', 'traslado_detalles', 'traslados', 'lote_rangos', 'lotes', 'compras', 'denominaciones', 'tipo_especies', 'distritos'] as $table) {
            Schema::dropIfExists($table);
        }
        Schema::enableForeignKeyConstraints();

        Schema::create('distritos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('codigo', 10)->unique();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('tipo_especies', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('denominaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_especie_id')->constrained('tipo_especies');
            $table->decimal('valor', 10, 2);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->string('numero_factura', 50);
            $table->decimal('monto_total', 12, 2)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('lotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compra_id')->constrained('compras');
            $table->foreignId('tipo_especie_id')->constrained('tipo_especies');
            $table->foreignId('denominacion_id')->constrained('denominaciones');
            $table->string('serie', 5)->nullable();
            $table->unsignedInteger('cantidad_total');
            $table->timestamps();
        });

        Schema::create('lote_rangos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('lotes');
            $table->unsignedInteger('numero_inicio');
            $table->unsignedInteger('numero_fin');
        });

        Schema::create('traslados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distrito_id')->constrained('distritos');
            $table->date('fecha');
            $table->text('observaciones')->nullable();
            $table->foreignId('usuario_id')->constrained('users');
            $table->timestamps();
        });

        Schema::create('traslado_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('traslado_id')->constrained('traslados');
            $table->foreignId('lote_id')->constrained('lotes');
            $table->unsignedInteger('numero_inicio');
            $table->unsignedInteger('numero_fin');
            $table->unsignedInteger('cantidad');
        });

        // nulas ahora ocurren en el distrito (post-traslado) y referencian traslado_detalles
        Schema::create('nulas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('traslado_detalle_id')->constrained('traslado_detalles');
            $table->foreignId('distrito_id')->constrained('distritos');
            $table->unsignedInteger('numero_inicio');
            $table->unsignedInteger('numero_fin');
            $table->date('fecha');
            $table->string('motivo', 255)->nullable();
            $table->foreignId('usuario_id')->constrained('users');
            $table->timestamps();
        });

        Schema::create('realizaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('numero_serie');
            $table->foreignId('tipo_especie_id')->constrained('tipo_especies');
            $table->foreignId('denominacion_id')->constrained('denominaciones');
            $table->foreignId('distrito_id')->constrained('distritos');
            $table->date('fecha');
            $table->string('nombre_contribuyente', 200)->nullable();
            $table->decimal('monto_cobrado', 10, 2);
            $table->foreignId('usuario_id')->constrained('users');
            $table->timestamps();
            // mismo número puede existir en tipos distintos; no se puede realizar dos veces en el mismo tipo
            $table->unique(['numero_serie', 'tipo_especie_id']);
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        foreach (['realizaciones', 'nulas', 'traslado_detalles', 'traslados', 'lote_rangos', 'lotes', 'compras', 'denominaciones', 'tipo_especies', 'distritos'] as $table) {
            Schema::dropIfExists($table);
        }
        Schema::enableForeignKeyConstraints();
    }
};
