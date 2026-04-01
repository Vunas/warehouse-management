<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'username' => fake()->userName(),
            'full_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'password' => Hash::make('password'),
            'is_active' => true,
        ];
    }

    public function withRole($roleName)
    {
        return $this->afterCreating(function (User $user) use ($roleName) {
            $role = \Spatie\Permission\Models\Role::where('name', $roleName)->first();

            if ($role) {
                $user->assignRole($role);
            }
        });
    }

    public function inactive()
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }
}
