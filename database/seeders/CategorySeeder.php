<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Gỗ', 'created_at' => now()],
            ['name' => 'Sắt', 'created_at' => now()],
            ['name' => 'Thép', 'created_at' => now()],
            ['name' => 'Vải', 'created_at' => now()],
        ];

        foreach ($categories as $cat) {
            DB::table('categories')->updateOrInsert(['name' => $cat['name']], $cat);
        }
    }
}