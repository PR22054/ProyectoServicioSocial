<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesSeeder::class);

        $admin = User::create([
            'usuario'  => 'admin',
            'password' => 'admin123',
            'rol'      => 'admin',
        ]);
        $admin->assignRole('admin');

        $empleado = User::create([
            'usuario'  => 'empleado',
            'password' => 'empleado123',
            'rol'      => 'empleado',
        ]);
        $empleado->assignRole('empleado');
    }
}
