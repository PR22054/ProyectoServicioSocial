<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // La validacion siempre acepto 10 caracteres; la columna solo tenia 5
        Schema::table('lotes', function (Blueprint $table) {
            $table->string('serie', 10)->nullable()->change();
        });

        DB::table('lotes')->update(['serie' => DB::raw("NULLIF(UPPER(TRIM(serie)), '')")]);

        // Al normalizar, dos lotes del mismo tipo y serie no pueden quedar con numeros repetidos
        $choques = DB::select("
            SELECT la.id a, lb.id b FROM lote_rangos ra
            JOIN lotes la ON la.id = ra.lote_id
            JOIN lote_rangos rb ON rb.id > ra.id
            JOIN lotes lb ON lb.id = rb.lote_id
            WHERE la.tipo_especie_id = lb.tipo_especie_id AND la.serie <=> lb.serie
              AND ra.numero_inicio <= rb.numero_fin AND ra.numero_fin >= rb.numero_inicio");
        if ($choques) {
            throw new RuntimeException('Hay lotes del mismo tipo y serie con numeros repetidos: '
                . collect($choques)->map(fn($c) => "{$c->a}/{$c->b}")->implode(', '));
        }

        Schema::table('realizaciones', function (Blueprint $table) {
            $table->string('serie', 10)->nullable()->after('denominacion_id');
            $table->index(['tipo_especie_id', 'serie', 'numero_inicio']);
        });

        // La serie de cada realizacion existente es la del lote que su distrito recibio con ese rango
        foreach (DB::table('realizaciones')->get() as $r) {
            $series = DB::table('traslado_detalles as td')
                ->join('traslados as t', 't.id', '=', 'td.traslado_id')
                ->join('lotes as l', 'l.id', '=', 'td.lote_id')
                ->where('t.distrito_id', $r->distrito_id)
                ->where('l.tipo_especie_id', $r->tipo_especie_id)
                ->where('l.denominacion_id', $r->denominacion_id)
                ->where('td.numero_inicio', '<=', $r->numero_fin)
                ->where('td.numero_fin', '>=', $r->numero_inicio)
                ->distinct()->pluck('l.serie');

            if ($series->count() > 1) {
                throw new RuntimeException("La realizacion #{$r->id} coincide con varias series ("
                    . $series->map(fn($s) => $s ?? 'sin serie')->implode(', ') . '); corrija el dato antes de migrar.');
            }

            DB::table('realizaciones')->where('id', $r->id)->update(['serie' => $series->first()]);
        }
    }

    public function down(): void
    {
        Schema::table('realizaciones', function (Blueprint $table) {
            $table->dropIndex(['tipo_especie_id', 'serie', 'numero_inicio']);
            $table->dropColumn('serie');
        });

        Schema::table('lotes', function (Blueprint $table) {
            $table->string('serie', 5)->nullable()->change();
        });
    }
};
