<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Điện tử', 'created_at' => now()],
            ['name' => 'Gia dụng', 'created_at' => now()],
            ['name' => 'Thực phẩm khô', 'created_at' => now()],
        ];

        foreach ($categories as $cat) {
            DB::table('categories')->updateOrInsert(['name' => $cat['name']], $cat);
        }
    }
}