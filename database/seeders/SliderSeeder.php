<?php

namespace Database\Seeders;

use App\Models\Slider;
use App\Models\User;
use Illuminate\Database\Seeder;

class SliderSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('is_admin', true)->first();

        if (!$admin) {
            $this->command->warn('Skipping SliderSeeder: No admin user found.');
            return;
        }

        $sliders = [
            // Homepage Sliders
            [
                'title' => 'Summer Sale - Up to 50% Off',
                'description' => 'Don\'t miss our biggest summer sale! Thousands of items at unbeatable prices.',
                'image_url' => 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=1200&h=400&fit=crop',
                'link_type' => 'external',
                'link_value' => '/search?sale=true',
                'slider_type' => 'homepage',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Sell Your Electronics Fast',
                'description' => 'List your electronics today and reach thousands of buyers. Free listing!',
                'image_url' => 'https://images.unsplash.com/photo-1498049794561-7780e7231661?w=1200&h=400&fit=crop',
                'link_type' => 'category',
                'link_value' => 'electronics',
                'slider_type' => 'homepage',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Find Your Dream Car',
                'description' => 'Browse thousands of vehicles from trusted sellers. Financing available.',
                'image_url' => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=1200&h=400&fit=crop',
                'link_type' => 'category',
                'link_value' => 'vehicles',
                'slider_type' => 'homepage',
                'display_order' => 3,
                'is_active' => true,
            ],

            // Search Results Sliders
            [
                'title' => 'Featured: Premium Electronics',
                'description' => 'Check out our handpicked selection of premium electronics.',
                'image_url' => 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=1200&h=300&fit=crop',
                'link_type' => 'external',
                'link_value' => '/search?category=electronics&featured=true',
                'slider_type' => 'search_results',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Hot Deals This Week',
                'description' => 'Limited time offers on popular items. Act fast!',
                'image_url' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=1200&h=300&fit=crop',
                'link_type' => 'external',
                'link_value' => '/search?sort=price_asc',
                'slider_type' => 'search_results',
                'display_order' => 2,
                'is_active' => true,
            ],

            // Category Sliders
            [
                'title' => 'Fashion Week Special',
                'description' => 'Discover the latest trends in fashion. New arrivals daily.',
                'image_url' => 'https://images.unsplash.com/photo-1445205170230-053b83016050?w=1200&h=300&fit=crop',
                'link_type' => 'category',
                'link_value' => 'fashion',
                'slider_type' => 'category',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Home Makeover Essentials',
                'description' => 'Transform your home with our curated collection.',
                'image_url' => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=1200&h=300&fit=crop',
                'link_type' => 'category',
                'link_value' => 'home-garden',
                'slider_type' => 'category',
                'display_order' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($sliders as $sliderData) {
            Slider::firstOrCreate(
                [
                    'title' => $sliderData['title'],
                    'slider_type' => $sliderData['slider_type'],
                ],
                array_merge($sliderData, ['created_by' => $admin->id])
            );
        }

        $this->command->info('SliderSeeder completed successfully.');
    }
}
