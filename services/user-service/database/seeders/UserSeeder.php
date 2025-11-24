<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'provider', // or 'admin' if you want
        ]);

        // Create Customers
        User::factory()->count(5)->create([
            'role' => 'customer',
        ]);

        // Create Providers
        User::factory()->count(3)->create([
            'role' => 'provider',
        ]);
    }
}
