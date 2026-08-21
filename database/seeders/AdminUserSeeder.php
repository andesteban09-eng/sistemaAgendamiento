<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'last_name' => 'Sistema',
            'email' => 'admin2@carvajal.com',
            'password' => Hash::make('Admin12345'),
            'rol' => 'administrador',
            'estado' => 'activo',
        ]);
    }
}
