<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(5)->withRole('staff')->create();
        User::factory(2)->withRole('customer')->inactive()->create();
    }
}
