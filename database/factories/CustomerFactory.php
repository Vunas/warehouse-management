<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    public function definition(): array
    {
        $user = User::factory()->create();

        return [
            'user_id' => $user->id, 
            'company_name' => $this->faker->company() . ' ' . $this->faker->companySuffix(),
            'tax_code' => $this->faker->unique()->numerify('##########'),
            'billing_phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'deleted_at' => null,
            'created_at' => now(),
        ];
    }
}