<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. DỮ LIỆU CỐ ĐỊNH 
        $catSteel = DB::table('categories')->where('name', 'Thép')->value('id');
        $catWood = DB::table('categories')->where('name', 'Gỗ')->value('id');

        $fixedProducts = [
            [
                'category_id' => $catSteel,
                'sku' => 'STL-001', 
                'name' => 'Thép Cuộn Cán Nguội (TEST)',
                'description' => 'Sản phẩm mẫu dùng để test quy đổi Size Lớn',
            ],
            [
                'category_id' => $catWood,
                'sku' => 'WOOD-001',
                'name' => 'Gỗ Pallet Thông (TEST)',
                'description' => 'Sản phẩm mẫu dùng để test quy đổi Size Vừa',
            ]
        ];

        foreach ($fixedProducts as $prod) {
            Product::updateOrCreate(['sku' => $prod['sku']], $prod);
        }

        // 2. DỮ LIỆU NGẪU NHIÊN (Factory) 
        Product::factory()->count(48)->create();

        echo "Seeded: 2 Fixed Products & 48 Random Products.\n";
    }
}