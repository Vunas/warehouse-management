<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        // 
        $categoryIds = DB::table('categories')->pluck('id')->toArray();

        $catId = !empty($categoryIds) ? $this->faker->randomElement($categoryIds) : 1;

        return [
            'category_id' => $catId,
            'sku' => strtoupper($this->faker->unique()->bothify('PROD-####')),
            'name' => $this->faker->words(3, true) . ' (' . $this->faker->colorName() . ')',
            'description' => $this->faker->sentence(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }
}
