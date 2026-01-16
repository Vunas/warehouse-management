<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseTypeSeeder extends Seeder
{
    public function run(): void
    {
        $whTypes = [
            [
                'code' => 'DRY_BIG', 
                'priority_rule' => 1, 
                'description' => 'Kho lớn thường (Chứa slot hàng tổng)',
                'created_at' => now()
            ],
            [
                'code' => 'DRY_SMALL', 
                'priority_rule' => 0, 
                'description' => 'Kho nhỏ thường (Chứa riêng 1 loại hàng)',
                'created_at' => now()
            ],
            [
                'code' => 'TRANSIT', 
                'priority_rule' => 1, 
                'description' => 'Kho trung chuyển (Nhận/Xuất hàng tạm thời)',
                'created_at' => now()
            ]
        ];

        foreach ($whTypes as $type) {
            DB::table('warehouse_types')->updateOrInsert(['code' => $type['code']], $type);
        }
    }
}