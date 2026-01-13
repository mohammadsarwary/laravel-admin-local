<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'John Smith',
                'email' => 'john@example.com',
                'phone' => '+1234567890',
                'password' => Hash::make('password123'),
                'location' => 'New York, NY',
                'bio' => 'Tech enthusiast and frequent seller.',
                'avatar' => 'https://i.pravatar.cc/150?img=1',
                'is_verified' => true,
                'is_active' => true,
                'is_admin' => false,
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah@example.com',
                'phone' => '+1234567891',
                'password' => Hash::make('password123'),
                'location' => 'Los Angeles, CA',
                'bio' => 'Fashion lover and boutique owner.',
                'avatar' => 'https://i.pravatar.cc/150?img=5',
                'is_verified' => true,
                'is_active' => true,
                'is_admin' => false,
            ],
            [
                'name' => 'Mike Williams',
                'email' => 'mike@example.com',
                'phone' => '+1234567892',
                'password' => Hash::make('password123'),
                'location' => 'Chicago, IL',
                'bio' => 'Car dealer with 10+ years experience.',
                'avatar' => 'https://i.pravatar.cc/150?img=3',
                'is_verified' => true,
                'is_active' => true,
                'is_admin' => false,
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily@example.com',
                'phone' => '+1234567893',
                'password' => Hash::make('password123'),
                'location' => 'Houston, TX',
                'bio' => 'Interior designer and home decor seller.',
                'avatar' => 'https://i.pravatar.cc/150?img=9',
                'is_verified' => true,
                'is_active' => true,
                'is_admin' => false,
            ],
            [
                'name' => 'David Brown',
                'email' => 'david@example.com',
                'phone' => '+1234567894',
                'password' => Hash::make('password123'),
                'location' => 'Phoenix, AZ',
                'bio' => 'Real estate investor.',
                'avatar' => 'https://i.pravatar.cc/150?img=11',
                'is_verified' => true,
                'is_active' => true,
                'is_admin' => false,
            ],
            [
                'name' => 'Jessica Wilson',
                'email' => 'jessica@example.com',
                'phone' => '+1234567895',
                'password' => Hash::make('password123'),
                'location' => 'Miami, FL',
                'bio' => 'Pet lover and animal rescuer.',
                'avatar' => 'https://i.pravatar.cc/150?img=23',
                'is_verified' => true,
                'is_active' => true,
                'is_admin' => false,
            ],
            [
                'name' => 'Robert Taylor',
                'email' => 'robert@example.com',
                'phone' => '+1234567896',
                'password' => Hash::make('password123'),
                'location' => 'Seattle, WA',
                'bio' => 'Software developer and tech seller.',
                'avatar' => 'https://i.pravatar.cc/150?img=12',
                'is_verified' => true,
                'is_active' => true,
                'is_admin' => false,
            },
            [
                'name' => 'Amanda Martinez',
                'email' => 'amanda@example.com',
                'phone' => '+1234567897',
                'password' => Hash::make('password123'),
                'location' => 'San Francisco, CA',
                'bio' => 'Fashion blogger and vintage collector.',
                'avatar' => 'https://i.pravatar.cc/150?img=25',
                'is_verified' => true,
                'is_active' => true,
                'is_admin' => false,
            ],
            [
                'name' => 'Christopher Lee',
                'email' => 'chris@example.com',
                'phone' => '+1234567898',
                'password' => Hash::make('password123'),
                'location' => 'Denver, CO',
                'bio' => 'Outdoor enthusiast and sports gear seller.',
                'avatar' => 'https://i.pravatar.cc/150?img=14',
                'is_verified' => true,
                'is_active' => true,
                'is_admin' => false,
            ],
            [
                'name' => 'Michelle Anderson',
                'email' => 'michelle@example.com',
                'phone' => '+1234567899',
                'password' => Hash::make('password123'),
                'location' => 'Austin, TX',
                'bio' => 'Book collector and literature enthusiast.',
                'avatar' => 'https://i.pravatar.cc/150?img=32',
                'is_verified' => true,
                'is_active' => true,
                'is_admin' => false,
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, [
                    'email_verified_at' => now(),
                    'rating' => rand(35, 50) / 10,
                    'review_count' => rand(5, 50),
                    'active_listings' => 0,
                    'sold_items' => rand(0, 20),
                    'followers' => rand(10, 100),
                ])
            );
        }

        $this->command->info('UserSeeder completed successfully. Created ' . count($users) . ' users.');
    }
}
