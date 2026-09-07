<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('traslados', function (Blueprint $table) {
            // Tipo: bodega_distrito (original), distrito_bodega (devolucion), distrito_distrito (inter-distrito)
            $table->string('tipo', 20)->default('bodega_distrito')->after('id');
            $table->unsignedBigInteger('origen_distrito_id')->nullable()->after('distrito_id');
            $table->foreign('origen_distrito_id')->references('id')->on('distritos');
        });

        // Permite NULL en distrito_id para devoluciones a bodega (destino implicito)
        DB::statement('ALTER TABLE traslados MODIFY COLUMN distrito_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        Schema::table('traslados', function (Blueprint $table) {
            $table->dropForeign(['origen_distrito_id']);
            $table->dropColumn(['tipo', 'origen_distrito_id']);
        });
        DB::statement('ALTER TABLE traslados MODIFY COLUMN distrito_id BIGINT UNSIGNED NOT NULL');
    }
};
