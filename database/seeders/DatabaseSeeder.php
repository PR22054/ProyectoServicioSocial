<?php

namespace Database\Seeders;

//seeder principal: roles, catalogo de especies, usuarios admin y empleado, y los datos iniciales de especies
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        //primero crea  roles para poder asignarlos a usuarios
        $this->call(RolesSeeder::class);
        $this->call(EspeciesMunicipalesSeeder::class);
        $this->call(DenominacionesSeeder::class);
        $this->call(CatalogoEspeciesSeeder::class);

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

        // movimientos de nov-2025 a mar-2026 de los libros de Tesoreria (necesita un usuario admin)
        $this->call(DatosInicialesEspeciesSeeder::class);
    }
}
