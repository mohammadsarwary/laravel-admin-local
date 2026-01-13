<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // NOTE: After adding new seeder classes, run: composer dump-autoload
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            AdminUserSeeder::class,
            UserSeeder::class,
            AdSeeder::class,
            SliderSeeder::class,
        ]);
    }
}
