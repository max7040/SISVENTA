<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@sisventa.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin12345'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        // Vendedor
        User::updateOrCreate(
            ['email' => 'vendedor@sisventa.com'],
            [
                'name' => 'Vendedor',
                'password' => Hash::make('vendedor12345'),
                'role' => User::ROLE_VENDEDOR,
            ]
        );
    }
}
