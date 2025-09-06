<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FoodItemsSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Popcorn', 'Drinks', 'Snacks', 'Combo'];

        for ($i = 1; $i <= 20; $i++) {
            DB::table('food_items')->insert([
                'name'        => 'Item ' . $i,
                'description' => fake()->sentence(10),
                'price'       => fake()->randomFloat(2, 20, 200), // 20.00 -> 200.00
                'category'    => $categories[array_rand($categories)],
                'image_url'   => fake()->imageUrl(640, 480, 'food', true),
                'is_available'=> fake()->boolean(80), // 80% true
                'created_at'  => now(),
            ]);
        }
    }
}
