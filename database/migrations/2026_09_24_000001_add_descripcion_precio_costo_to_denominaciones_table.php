<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

//agrega la descripcion y el precio de costo a las denominaciones
return new class extends Migration
{
    //agrega las columnas
    public function up(): void
    {
        Schema::table('denominaciones', function (Blueprint $table) {
            //texto descriptivo que usan los libros ("DE 30 COLONES $ 3.43", "FORMULAS CONTINUAS")
            $table->string('descripcion', 150)->nullable()->after('tipo_especie_id');
            //P. DE COSTO: precio de adquisicion al M.H., base de la columna DESCARGOS.
            //4 decimales porque la referencia usa valores como 0.0105
            $table->decimal('precio_costo', 10, 4)->nullable()->after('valor');
        });
    }

    //quita las columnas
    public function down(): void
    {
        Schema::table('denominaciones', function (Blueprint $table) {
            $table->dropColumn(['descripcion', 'precio_costo']);
        });
    }
};
