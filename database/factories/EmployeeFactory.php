<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class EmployeeFactory extends Factory
{
    public function definition(): array
    {
        $user = User::factory()->create();

        $warehouseIds = DB::table('warehouses')->pluck('id')->toArray();
        $whId = !empty($warehouseIds) ? $this->faker->randomElement($warehouseIds) : null;

        return [
            'user_id' => $user->id,
            'position' => $this->faker->jobTitle(),
            'warehouse_id' => $whId,
            'hired_at' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'deleted_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}