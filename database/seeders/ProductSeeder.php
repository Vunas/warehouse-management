<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Categories (Danh mục phụ tùng)
        DB::table('categories')->insert([
            ['id' => 1, 'name' => 'Động cơ', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Hệ thống phanh', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Hệ thống điện', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Lọc & dầu nhớt', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 2. Brands (Hãng phụ tùng)
        DB::table('brands')->insert([
            ['id' => 1, 'name' => 'Bosch', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Denso', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Toyota Genuine Parts', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 3. Products (Sản phẩm phụ tùng)
        DB::table('products')->insert([
            [
                'id' => 1,
                'category_id' => 1,
                'brand_id' => 1,
                'name' => 'Bugi Bosch Platinum',
                'description' => 'Bugi cao cấp giúp đánh lửa ổn định cho động cơ.',
                'price' => 150000.00,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 2,
                'category_id' => 2,
                'brand_id' => 2,
                'name' => 'Má phanh Denso',
                'description' => 'Má phanh chất lượng cao, độ bền cao.',
                'price' => 800000.00,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 3,
                'category_id' => 3,
                'brand_id' => 3,
                'name' => 'Ắc quy Toyota 12V',
                'description' => 'Ắc quy chính hãng Toyota, dung lượng ổn định.',
                'price' => 1800000.00,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 4,
                'category_id' => 4,
                'brand_id' => 1,
                'name' => 'Lọc dầu Bosch',
                'description' => 'Lọc dầu giúp bảo vệ động cơ khỏi cặn bẩn.',
                'price' => 120000.00,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
