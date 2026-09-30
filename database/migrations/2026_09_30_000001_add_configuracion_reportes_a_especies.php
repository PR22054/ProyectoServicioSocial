<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Configuracion que necesitan los reportes para salir igual a los libros de cada distrito
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tipo_especies', function (Blueprint $table) {
            // formulas, cartas, guias y tarjetas de control se llevan en unidades, sin valor
            $table->boolean('unidades')->default(false)->after('descripcion');
            $table->unsignedSmallInteger('orden')->default(0)->after('unidades');
        });

        Schema::table('denominaciones', function (Blueprint $table) {
            // null: la especie no se vende (solo se descarga a costo)
            $table->decimal('precio_venta', 10, 4)->nullable()->after('precio_costo');
        });

        Schema::table('distritos', function (Blueprint $table) {
            $table->string('nombre_oficial', 150)->nullable()->after('nombre');
            $table->string('lugar_firma', 150)->nullable()->after('nombre_oficial');
            // saldo del libro ordenado por valor, o en el orden en que entraron los documentos
            $table->boolean('saldo_por_valor')->default(true)->after('lugar_firma');
        });

        // Como llama cada distrito a cada tipo en su libro y en su reporte mensual, y en que orden lo lista
        Schema::create('distrito_tipo_especie', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distrito_id')->constrained('distritos');
            $table->foreignId('tipo_especie_id')->constrained('tipo_especies');
            $table->string('titulo_libro', 150)->nullable();
            $table->string('titulo_reporte', 150)->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            // el libro suma las nulas al total de realizaciones en vez de descontarlas aparte
            $table->boolean('nulas_en_realizaciones')->default(false);
            $table->timestamps();
            $table->unique(['distrito_id', 'tipo_especie_id']);
        });

        // Tarifa de cada denominacion en cada distrito: rotulo, costo, venta y fila de nulas del reporte mensual
        Schema::create('distrito_denominacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distrito_id')->constrained('distritos');
            $table->foreignId('denominacion_id')->constrained('denominaciones');
            $table->string('descripcion', 150)->nullable();
            $table->decimal('precio_costo', 10, 4)->nullable();
            $table->decimal('precio_venta', 10, 4)->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->string('descripcion_nulas', 150)->nullable();
            $table->unsignedSmallInteger('orden_nulas')->nullable();
            $table->boolean('nulas_con_venta')->default(false);
            $table->timestamps();
            $table->unique(['distrito_id', 'denominacion_id']);
        });

        // Cajas o puntos de cobro dentro de un distrito (CAJA 1, RASTRO...)
        Schema::create('ubicaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distrito_id')->constrained('distritos');
            $table->string('nombre', 60);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
            $table->unique(['distrito_id', 'nombre']);
        });

        // Desde una fecha, un rango queda en una caja; no mueve inventario, solo rotula el libro
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

    public function down(): void
    {
        Schema::dropIfExists('ubicacion_rangos');
        Schema::dropIfExists('ubicaciones');
        Schema::dropIfExists('distrito_denominacion');
        Schema::dropIfExists('distrito_tipo_especie');

        Schema::table('distritos', fn(Blueprint $t) => $t->dropColumn(['nombre_oficial', 'lugar_firma', 'saldo_por_valor']));
        Schema::table('denominaciones', fn(Blueprint $t) => $t->dropColumn('precio_venta'));
        Schema::table('tipo_especies', fn(Blueprint $t) => $t->dropColumn(['unidades', 'orden']));
    }
};
