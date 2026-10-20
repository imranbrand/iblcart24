<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {

        foreach ([
            [
                'name' => 'Jewelry',
                'slug' => 'jewelry',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fashion',
                'slug' => 'fashion',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Accessories',
                'slug' => 'accessories',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bags',
                'slug' => 'bags',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Shoes',
                'slug' => 'shoes',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gifts',
                'slug' => 'gifts',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ] as $row) {
            Category::updateOrCreate(['slug' => $row['slug']], $row);
        }
    }
}
