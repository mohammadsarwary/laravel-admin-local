<?php

namespace Database\Seeders;

use App\Enums\AdCondition;
use App\Models\Ad;
use App\Models\AdImage;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();
        $users = User::where('is_admin', false)->get();

        if ($categories->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Skipping AdSeeder: No categories or users found.');
            return;
        }

        $ads = [
            // Electronics
            [
                'title' => 'iPhone 15 Pro Max 256GB',
                'description' => 'Brand new iPhone 15 Pro Max, titanium blue, 256GB storage. Includes original box, charger, and 1-year warranty.',
                'price' => 1199.00,
                                'condition' => AdCondition::NEW->value,
                'location' => 'New York, NY',
                'category_slug' => 'electronics',
            ],
            [
                'title' => 'MacBook Pro 14" M3 Chip',
                'description' => 'MacBook Pro 14 inch with M3 Pro chip, 18GB RAM, 512GB SSD. Excellent condition, battery health 95%.',
                'price' => 1899.00,
                                'condition' => AdCondition::LIKE_NEW->value,
                'location' => 'Los Angeles, CA',
                'category_slug' => 'electronics',
            ],
            [
                'title' => 'Sony PlayStation 5 Digital Edition',
                'description' => 'PS5 Digital Edition with 2 controllers and 5 games. Used for 6 months, works perfectly.',
                'price' => 399.00,
                                'condition' => AdCondition::GOOD->value,
                'location' => 'Chicago, IL',
                'category_slug' => 'electronics',
            ],
            [
                'title' => 'Samsung 65" QLED 4K Smart TV',
                'description' => 'Samsung QN65Q80B 65-inch QLED 4K Smart TV. Minor scratches on bezel, screen perfect.',
                'price' => 649.00,
                                'condition' => AdCondition::GOOD->value,
                'location' => 'Houston, TX',
                'category_slug' => 'electronics',
            ],

            // Vehicles
            [
                'title' => '2022 Toyota Camry SE',
                'description' => '2022 Toyota Camry SE, 25,000 miles, silver exterior, black interior. Clean title, single owner.',
                'price' => 24500.00,
                                'condition' => AdCondition::LIKE_NEW->value,
                'location' => 'Miami, FL',
                'category_slug' => 'vehicles',
            ],
            [
                'title' => '2019 Honda Civic EX',
                'description' => '2019 Honda Civic EX, 45,000 miles, well maintained. New tires, recent oil change, no accidents.',
                'price' => 18500.00,
                                'condition' => AdCondition::GOOD->value,
                'location' => 'Phoenix, AZ',
                'category_slug' => 'vehicles',
            ],
            [
                'title' => 'Tesla Model 3 Standard Range',
                'description' => '2021 Tesla Model 3 Standard Range Plus, 30,000 miles, autopilot included. Super clean.',
                'price' => 28900.00,
                                'condition' => AdCondition::GOOD->value,
                'location' => 'Seattle, WA',
                'category_slug' => 'vehicles',
            ],
            [
                'title' => 'Harley-Davidson Iron 883',
                'description' => '2020 Harley-Davidson Iron 883, 5,000 miles. Custom exhaust, LED lights, garage kept.',
                'price' => 8500.00,
                                'condition' => AdCondition::LIKE_NEW->value, // Must match enum: new, like_new, good, fair, poor
                'location' => 'Denver, CO',
                'category_slug' => 'vehicles',
            ],

            // Property
            [
                'title' => '2BR Apartment Downtown',
                'description' => 'Modern 2-bedroom apartment in downtown area. 1,200 sq ft, updated kitchen, in-unit laundry.',
                'price' => 1800.00,
                                'condition' => AdCondition::GOOD->value,
                'location' => 'San Francisco, CA',
                'category_slug' => 'property',
            ],
            [
                'title' => '3BR House with Backyard',
                'description' => 'Spacious 3-bedroom house with large backyard. 1,800 sq ft, 2 bathrooms, attached garage.',
                'price' => 2500.00,
                                'condition' => AdCondition::GOOD->value,
                'location' => 'Austin, TX',
                'category_slug' => 'property',
            ],
            [
                'title' => 'Studio Apartment Near Metro',
                'description' => 'Cozy studio apartment, 500 sq ft, fully furnished. Walking distance to metro station.',
                'price' => 1100.00,
                                'condition' => AdCondition::LIKE_NEW->value,
                'location' => 'Washington, DC',
                'category_slug' => 'property',
            ],

            // Fashion
            [
                'title' => 'Gucci Leather Handbag',
                'description' => 'Authentic Gucci leather handbag, black, used twice. Comes with dust bag and receipt.',
                'price' => 850.00,
                                'condition' => AdCondition::LIKE_NEW->value,
                'location' => 'New York, NY',
                'category_slug' => 'fashion',
            ],
            [
                'title' => 'Nike Air Jordan 1 Retro High',
                'description' => 'Nike Air Jordan 1 Retro High OG, Chicago colorway, size 10. Deadstock, never worn.',
                'price' => 320.00,
                                'condition' => AdCondition::NEW->value,
                'location' => 'Atlanta, GA',
                'category_slug' => 'fashion',
            ],
            [
                'title' => 'Levi\'s Vintage Denim Jacket',
                'description' => 'Vintage Levi\'s denim jacket, size M, excellent condition. Classic 90s style.',
                'price' => 120.00,
                                'condition' => AdCondition::GOOD->value,
                'location' => 'Portland, OR',
                'category_slug' => 'fashion',
            ],

            // Home & Garden
            [
                'title' => 'IKEA Sectional Sofa',
                'description' => 'IKEA KIVIK sectional sofa, 3-seat with chaise, dark gray. 2 years old, minor wear.',
                'price' => 350.00,
                                'condition' => AdCondition::GOOD->value,
                'location' => 'Boston, MA',
                'category_slug' => 'home-garden',
            ],
            [
                'title' => 'Dining Table Set for 6',
                'description' => 'Solid wood dining table with 6 chairs. Modern design, excellent condition.',
                'price' => 450.00,
                                'condition' => AdCondition::LIKE_NEW->value,
                'location' => 'Philadelphia, PA',
                'category_slug' => 'home-garden',
            ],
            [
                'title' => 'Outdoor Patio Furniture Set',
                'description' => '4-piece patio furniture set: table, 2 chairs, loveseat. Weather-resistant cushions.',
                'price' => 280.00,
                                'condition' => AdCondition::GOOD->value,
                'location' => 'San Diego, CA',
                'category_slug' => 'home-garden',
            ],

            // Sports
            [
                'title' => 'Trek Mountain Bike',
                'description' => 'Trek Marlin 7 mountain bike, medium frame. Well maintained, recently serviced.',
                'price' => 450.00,
                                'condition' => AdCondition::GOOD->value,
                'location' => 'Minneapolis, MN',
                'category_slug' => 'sports',
            ],
            [
                'title' => 'Wilson Tennis Racket Set',
                'description' => '2 Wilson tennis rackets, 3 cans of balls, carrying bag. Used for one season.',
                'price' => 120.00,
                                'condition' => AdCondition::GOOD->value,
                'location' => 'Tampa, FL',
                'category_slug' => 'sports',
            ],
            [
                'title' => 'Yoga Mat Premium Quality',
                'description' => 'Extra thick yoga mat, non-slip surface, carrying strap included. Like new.',
                'price' => 45.00,
                                'condition' => AdCondition::LIKE_NEW->value,
                'location' => 'Nashville, TN',
                'category_slug' => 'sports',
            ],

            // Books
            [
                'title' => 'Programming Books Collection',
                'description' => 'Collection of 15 programming books: Python, JavaScript, React, Node.js, AWS. Like new.',
                'price' => 150.00,
                                'condition' => AdCondition::LIKE_NEW->value,
                'location' => 'San Jose, CA',
                'category_slug' => 'books',
            ],
            [
                'title' => 'Harry Potter Complete Set',
                'description' => 'Harry Potter complete book set, 1st editions. Excellent condition, dust jackets intact.',
                'price' => 200.00,
                                'condition' => AdCondition::GOOD->value,
                'location' => 'Orlando, FL',
                'category_slug' => 'books',
            ],

            // Pets
            [
                'title' => 'Dog Crate Large Size',
                'description' => 'Large dog crate, 42 inches, double door. Used for 6 months, clean and functional.',
                'price' => 75.00,
                                'condition' => AdCondition::GOOD->value,
                'location' => 'Dallas, TX',
                'category_slug' => 'pets',
            ],
            [
                'title' => 'Cat Tree Tower 5-Level',
                'description' => '5-level cat tree tower, scratching posts, condo, perches. Minor scratches, sturdy.',
                'price' => 85.00,
                                'condition' => AdCondition::GOOD->value,
                'location' => 'Charlotte, NC',
                'category_slug' => 'pets',
            ],

            // Jobs
            [
                'title' => 'Freelance Web Developer Needed',
                'description' => 'Looking for an experienced web developer for a 3-month project. React and Node.js required.',
                'price' => 5000.00,
                                'condition' => AdCondition::NEW->value,
                'location' => 'Remote',
                'category_slug' => 'jobs',
            ],
            [
                'title' => 'Graphic Designer for Logo',
                'description' => 'Need a professional logo design for a startup. Multiple revisions expected.',
                'price' => 300.00,
                                'condition' => AdCondition::NEW->value,
                'location' => 'Remote',
                'category_slug' => 'jobs',
            ],

            // Services
            [
                'title' => 'Professional House Cleaning',
                'description' => 'Experienced house cleaner offering deep cleaning services. Licensed and insured.',
                'price' => 150.00,
                                'condition' => AdCondition::NEW->value,
                'location' => 'Las Vegas, NV',
                'category_slug' => 'services',
            ],
            [
                'title' => 'Mobile Car Detailing',
                'description' => 'Full car detailing service at your location. Interior and exterior cleaning.',
                'price' => 100.00,
                                'condition' => AdCondition::NEW->value,
                'location' => 'Phoenix, AZ',
                'category_slug' => 'services',
            ],
        ];

        foreach ($ads as $adData) {
            $category = $categories->firstWhere('slug', $adData['category_slug']);
            $user = $users->random();

            if ($category) {
                $ad = Ad::create([
                    'user_id' => $user->id,
                    'category_id' => $category->id,
                    'title' => $adData['title'],
                    'description' => $adData['description'],
                    'price' => $adData['price'],
                    'condition' => $adData['condition'],
                    'location' => $adData['location'],
                    'status' => 'active',
                    'views' => rand(10, 500),
                    'favorites' => rand(0, 50),
                ]);

                // Create images for the ad
                $imageKeywords = $this->getImageKeywords($adData['category_slug']);
                $numImages = rand(1, 4);

                for ($i = 0; $i < $numImages; $i++) {
                    AdImage::create([
                        'ad_id' => $ad->id,
                        'image_url' => "https://loremflickr.com/800/600/{$imageKeywords}?random={$ad->id}{$i}",
                        'display_order' => $i,
                        'is_primary' => $i === 0,
                    ]);
                }

                $user->incrementStat('active_listings');
            }
        }

        $this->command->info('AdSeeder completed successfully.');
    }

    private function getImageKeywords(string $categorySlug): string
    {
        return match ($categorySlug) {
            'electronics' => 'electronics,technology',
            'vehicles' => 'car,vehicle',
            'property' => 'house,building',
            'fashion' => 'clothing,fashion',
            'home-garden' => 'furniture,home',
            'sports' => 'sports,equipment',
            'books' => 'book,library',
            'pets' => 'dog,animal',
            'jobs' => 'office,business',
            'services' => 'professional,service',
            default => 'product',
        };
    }
}
