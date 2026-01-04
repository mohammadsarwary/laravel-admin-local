<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@marketlocal.com'],
            [
                'name' => 'Super Admin',
                'email' => 'admin@marketlocal.com',
                'password' => Hash::make('admin123'),
                'is_admin' => true,
                'admin_role' => 'super_admin',
                'is_verified' => true,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'moderator@marketlocal.com'],
            [
                'name' => 'Moderator',
                'email' => 'moderator@marketlocal.com',
                'password' => Hash::make('moderator123'),
                'is_admin' => true,
                'admin_role' => 'moderator',
                'is_verified' => true,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
