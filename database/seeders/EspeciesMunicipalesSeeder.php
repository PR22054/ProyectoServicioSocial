<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EspeciesMunicipalesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('distritos')->insert([
            ['nombre' => 'Distrito Metapán',                'codigo' => 'D01', 'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Distrito Masahuat',               'codigo' => 'D02', 'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Distrito Santa Rosa Guachipilín', 'codigo' => 'D03', 'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Distrito Texistepeque',           'codigo' => 'D04', 'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('tipo_especies')->insert([
            ['nombre' => 'Fondo Vialidad',                         'descripcion' => null,                                      'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Tiquetes de Mercado',                    'descripcion' => null,                                      'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Tiquetes Servicio de Báscula',           'descripcion' => null,                                      'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Cartas de Venta Continua',               'descripcion' => null,                                      'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Carnet de Menores',                      'descripcion' => 'Ya no se realizan en varios distritos',   'activo' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Tarjetas para Carnet de Identificación', 'descripcion' => null,                                      'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Fórmulas 1-ISAM Plana',                  'descripcion' => null,                                      'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Fórmulas 1-ISAM Continua',               'descripcion' => null,                                      'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Títulos a Perpetuidad',                  'descripcion' => null,                                      'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Guías de Conducción de Ganado',          'descripcion' => null,                                      'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Boletos de Cobro de Material Pétreo',    'descripcion' => null,                                      'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Tarjetas de Inmuebles',                  'descripcion' => null,                                      'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Tarjetas de Empresa',                    'descripcion' => null,                                      'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Avisos de Cobro Común',                  'descripcion' => null,                                      'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Tarjetas de Control de Mercado',         'descripcion' => null,                                      'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Caja Auxiliar',                          'descripcion' => null,                                      'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // denominaciones de Fondo Vialidad (tipo_especie_id = 1)
        $vialidadId = DB::table('tipo_especies')->where('nombre', 'Fondo Vialidad')->value('id');
        DB::table('denominaciones')->insert([
            ['tipo_especie_id' => $vialidadId, 'valor' => 0.57, 'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['tipo_especie_id' => $vialidadId, 'valor' => 0.69, 'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['tipo_especie_id' => $vialidadId, 'valor' => 1.14, 'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['tipo_especie_id' => $vialidadId, 'valor' => 1.71, 'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['tipo_especie_id' => $vialidadId, 'valor' => 2.29, 'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['tipo_especie_id' => $vialidadId, 'valor' => 2.86, 'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['tipo_especie_id' => $vialidadId, 'valor' => 3.43, 'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
