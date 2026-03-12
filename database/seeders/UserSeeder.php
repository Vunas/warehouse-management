<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'username'  => 'admin',
                'full_name' => 'Administrator',
                'phone'     => '0123456789',
                'password'  => Hash::make('123456'),
                'is_active' => true,
            ]
        );

        User::factory(50)->create();

        User::factory(5)->inactive()->create();
    }
}
