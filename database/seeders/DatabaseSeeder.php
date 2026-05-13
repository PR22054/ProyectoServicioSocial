<?php

namespace Database\Seeders;

//seeder principal, llama a RolesSeeder. Crea los usuarios admin y empleado
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        //primero crea  roles para poder asignarlos a usuarios
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
