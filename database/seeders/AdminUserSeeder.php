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
            ['email' => 'admin@bazarino.store'],
            [
                'name' => 'Super Admin',
                'email' => 'admin@bazarino.store',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'admin_role' => 'super_admin',
                'is_verified' => true,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'moderator@bazarino.store'],
            [
                'name' => 'Moderator',
                'email' => 'moderator@bazarino.store',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'admin_role' => 'moderator',
                'is_verified' => true,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
