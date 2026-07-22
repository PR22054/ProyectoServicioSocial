<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DenominacionesSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = DB::table('tipo_especies')->pluck('id', 'nombre');

        $denominaciones = [
            'Tiquetes de Mercado' => [0.12, 0.15, 0.25, 0.50, 1.00],
            'Tiquetes Servicio de Báscula' => [0.50],
            'Cartas de Venta Continua' => [0.15],
            'Carnet de Menores' => [0.06, 0.09],
            'Tarjetas para Carnet de Identificación' => [0.30],
            'Fórmulas 1-ISAM Plana' => [0.06],
            'Fórmulas 1-ISAM Continua' => [0.10],
            'Títulos a Perpetuidad' => [0.53],
            'Guías de Conducción de Ganado' => [0.20],
            'Boletos de Cobro de Material Pétreo' => [0.06],
            'Tarjetas de Inmuebles' => [0.26],
            'Tarjetas de Empresa' => [0.26],
            'Avisos de Cobro Común' => [0.10],
            'Tarjetas de Control de Mercado' => [0.30],
        ];

        foreach ($denominaciones as $nombreTipo => $valores) {
            $tipoId = $tipos[$nombreTipo] ?? null;
            if (!$tipoId) continue;

            foreach ($valores as $valor) {
                // activo=0 solo para Carnet de Menores
                $activo = ($nombreTipo === 'Carnet de Menores') ? 0 : 1;

                $existe = DB::table('denominaciones')
                    ->where('tipo_especie_id', $tipoId)
                    ->where('valor', $valor)
                    ->exists();

                if (!$existe) {
                    DB::table('denominaciones')->insert([
                        'tipo_especie_id' => $tipoId,
                        'valor'           => $valor,
                        'activo'          => $activo,
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ]);
                }
            }
        }
    }
}
