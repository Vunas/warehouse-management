<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'username' => $this->faker->unique()->userName(),
            'password' => Hash::make('123456'), 
            'full_name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}