<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class InventoryItemFactory extends Factory
{
    public function definition(): array
    {
        $blockIds = DB::table('storage_blocks')->where('status', '!=', 'Locked')->pluck('id')->toArray();
        $productIds = DB::table('products')->pluck('id')->toArray();

        $blockId = !empty($blockIds) ? $this->faker->randomElement($blockIds) : 1;
        $prodId = !empty($productIds) ? $this->faker->randomElement($productIds) : 1;

        return [
            'block_id' => $blockId,
            'product_id' => $prodId,
            'calc_id' => null,
            'slot_used' => $this->faker->numberBetween(1, 10),
            'imported_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'current_quantity' => $this->faker->numberBetween(1, 50),
            'deleted_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
