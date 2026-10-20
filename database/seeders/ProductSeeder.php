<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {

        $jewelry = Category::where('slug', 'jewelry')->first();
        $fashion = Category::where('slug', 'fashion')->first();
        $accessories = Category::where('slug', 'accessories')->first();
        $bags = Category::where('slug', 'bags')->first();
        $shoes = Category::where('slug', 'shoes')->first();
        $gifts = Category::where('slug', 'gifts')->first();

        foreach ([
            [
                'category_id' => $jewelry?->id,
                'name' => 'Luxury Ring',
                'description' => 'Premium quality luxury ring.',
                'price' => 999,
                'image' => 'ring.jpg',
                'stock' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'category_id' => $jewelry?->id,
                'name' => 'Pearl Bracelet',
                'description' => 'Elegant pearl bracelet.',
                'price' => 799,
                'image' => 'bracelet.jpg',
                'stock' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'category_id' => $jewelry?->id,
                'name' => 'Silver Necklace',
                'description' => 'Beautiful silver necklace.',
                'price' => 1499,
                'image' => 'necklace.jpg',
                'stock' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'category_id' => $accessories?->id,
                'name' => 'Classic Watch',
                'description' => 'Premium wrist watch.',
                'price' => 2499,
                'image' => 'watch.jpg',
                'stock' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'category_id' => $bags?->id,
                'name' => 'Ladies Hand Bag',
                'description' => 'Stylish ladies handbag.',
                'price' => 1899,
                'image' => 'bag.jpg',
                'stock' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'category_id' => $shoes?->id,
                'name' => 'Sneakers',
                'description' => 'Comfortable casual sneakers.',
                'price' => 2199,
                'image' => 'shoe.jpg',
                'stock' => 14,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'category_id' => $fashion?->id,
                'name' => 'Men T-Shirt',
                'description' => '100% Cotton T-Shirt.',
                'price' => 699,
                'image' => 'tshirt.jpg',
                'stock' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'category_id' => $gifts?->id,
                'name' => 'Gift Box',
                'description' => 'Perfect gift package.',
                'price' => 499,
                'image' => 'gift.jpg',
                'stock' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ] as $row) {
            Product::updateOrCreate(['name' => $row['name']], $row);
        }
    }
}