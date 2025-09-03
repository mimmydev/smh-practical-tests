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
            'name' => 'Admin User',
            'email' => 'admin@jobfair.com',
            'password' => Hash::make('password123'),
            'phone' => '+60123456789',
            'profession' => 'Administrator',
            'experience_level' => 'executive',
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        // Create sample regular user
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'phone' => '+60198765432',
            'profession' => 'Software Developer',
            'experience_level' => 'mid',
            'is_admin' => false,
            'email_verified_at' => now(),
        ]);
    }
}
