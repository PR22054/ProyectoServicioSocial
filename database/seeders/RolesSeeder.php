<?php

namespace Database\Seeders;

//crea los roles admin y empleado en la tabla de Spatie si aun no existen
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'empleado']);
        Role::firstOrCreate(['name' => 'visitante']);
    }
}
