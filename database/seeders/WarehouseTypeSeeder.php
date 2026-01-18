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
                'type_code' => 'TOTAL',
                'rentable' => true,
                'single_contract' => false,
                'priority_rule' => 1,
                'description' => 'Kho Tổng (Dry Big) - Lưu trữ chung, tính phí theo slot/block',
                'created_at' => now()
            ],
            [
                'type_code' => 'SMALL',
                'rentable' => true,
                'single_contract' => true,
                'priority_rule' => 0,
                'description' => 'Kho Nhỏ (Dry Small) - Cho thuê nguyên căn, 1 loại hàng',
                'created_at' => now()
            ],
            [
                'type_code' => 'TRANSIT',
                'rentable' => false,
                'single_contract' => false,
                'priority_rule' => 1,
                'description' => 'Kho Trung Chuyển - Vùng đệm nhập/xuất',
                'created_at' => now()
            ]
        ];

        foreach ($whTypes as $type) {
            DB::table('warehouse_types')->updateOrInsert(['type_code' => $type['type_code']], $type);
        }
    }
}
