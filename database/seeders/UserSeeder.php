<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Owner / Admin
        User::updateOrCreate(
            ['email' => 'owner@inventory.com'],
            [
                'name' => 'Admin Owner',
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'phone' => '081234567890',
                'is_active' => true,
            ]
        );

        // Kasir
        User::updateOrCreate(
            ['email' => 'kasir@inventory.com'],
            [
                'name' => 'Kasir 1',
                'password' => Hash::make('password123'),
                'role' => 'kasir',
                'phone' => '081234567891',
                'is_active' => true,
            ]
        );

        // Staff Gudang
        User::updateOrCreate(
            ['email' => 'staff@inventory.com'],
            [
                'name' => 'Staff Gudang 1',
                'password' => Hash::make('password123'),
                'role' => 'staff_gudang',
                'phone' => '081234567892',
                'is_active' => true,
            ]
        );
    }
}
