<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesSeeder::class);

        $user = User::create([
            'usuario'  => 'admin',
            'password' => 'admin123',
            'rol'      => 'admin',
        ]);

        $user->assignRole('admin');
    }
}
