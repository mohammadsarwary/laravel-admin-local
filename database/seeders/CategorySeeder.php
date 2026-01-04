<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics', 'slug' => 'electronics', 'icon' => 'devices', 'display_order' => 1],
            ['name' => 'Vehicles', 'slug' => 'vehicles', 'icon' => 'directions_car', 'display_order' => 2],
            ['name' => 'Property', 'slug' => 'property', 'icon' => 'home', 'display_order' => 3],
            ['name' => 'Fashion', 'slug' => 'fashion', 'icon' => 'checkroom', 'display_order' => 4],
            ['name' => 'Home & Garden', 'slug' => 'home-garden', 'icon' => 'yard', 'display_order' => 5],
            ['name' => 'Sports', 'slug' => 'sports', 'icon' => 'sports_soccer', 'display_order' => 6],
            ['name' => 'Books', 'slug' => 'books', 'icon' => 'menu_book', 'display_order' => 7],
            ['name' => 'Pets', 'slug' => 'pets', 'icon' => 'pets', 'display_order' => 8],
            ['name' => 'Jobs', 'slug' => 'jobs', 'icon' => 'work', 'display_order' => 9],
            ['name' => 'Services', 'slug' => 'services', 'icon' => 'build', 'display_order' => 10],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
