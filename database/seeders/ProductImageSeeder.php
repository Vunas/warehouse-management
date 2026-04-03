<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductImageSeeder extends Seeder
{
    public function run(): void
    {
        $products = DB::table('products')->pluck('id')->toArray();
        
        if (empty($products)) {
            return;
        }

        $now = Carbon::now();
        $images = [];

        foreach ($products as $productId) {
            // Random mỗi sản phẩm có từ 1 đến 2 hình ảnh
            $numImages = rand(1, 2);
            
            for ($i = 1; $i <= $numImages; $i++) {
                $images[] = [
                    'product_id' => $productId,
                    // Dùng picsum để sinh ra hình vuông 600x600 ngẫu nhiên làm ảnh sản phẩm
                    'image_url'  => 'https://picsum.photos/seed/product_' . $productId . '_' . $i . '/600/600',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('product_images')->insert($images);
    }
}