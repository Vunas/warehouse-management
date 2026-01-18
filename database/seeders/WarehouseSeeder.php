<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Lấy ID của các loại kho
        $typeTotal = DB::table('warehouse_types')->where('type_code', 'TOTAL')->value('id');
        $typeSmall = DB::table('warehouse_types')->where('type_code', 'SMALL')->value('id');
        $typeTransit = DB::table('warehouse_types')->where('type_code', 'TRANSIT')->value('id');

        // 2. Tạo KHO TỔNG (Zone A)
        $whTotalId = DB::table('warehouses')->insertGetId([
            'type_id' => $typeTotal,
            'name' => 'Kho Tổng A (Zone A)',
            'total_blocks' => 10,
            'total_slots' => 1000,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Tạo 10 Block cho Kho Tổng (Mỗi block 100 slot)
        for ($i = 1; $i <= 10; $i++) {
            DB::table('storage_blocks')->insert([
                'warehouse_id' => $whTotalId,
                'block_code' => 'A-' . str_pad($i, 2, '0', STR_PAD_LEFT), 
                'total_slots' => 100,
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Tạo KHO NHỎ (B1, B2)
        for ($i = 1; $i <= 2; $i++) {
            $whSmallId = DB::table('warehouses')->insertGetId([
                'type_id' => $typeSmall,
                'name' => "Kho Nhỏ B$i",
                'total_blocks' => 1,
                'total_slots' => 200,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Kho nhỏ chỉ có 1 Block duy nhất chiếm toàn bộ kho
            DB::table('storage_blocks')->insert([
                'warehouse_id' => $whSmallId,
                'block_code' => "B$i-WHOLE",
                'total_slots' => 200,
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 4. Tạo Cặp KHO TRUNG CHUYỂN (T1 & T2)
        $whT1 = DB::table('warehouses')->insertGetId([
            'type_id' => $typeTransit,
            'name' => 'Kho Trung Chuyển T1 (Nhập)',
            'total_blocks' => 1,
            'total_slots' => 50,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $whT2 = DB::table('warehouses')->insertGetId([
            'type_id' => $typeTransit,
            'name' => 'Kho Trung Chuyển T2 (Xuất)',
            'total_blocks' => 1,
            'total_slots' => 50,
            'status' => 'active',
            'paired_warehouse_id' => $whT1, // Link với T1
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update ngược lại T1 pair với T2
        DB::table('warehouses')->where('id', $whT1)->update(['paired_warehouse_id' => $whT2]);

        // Tạo Block cho kho trung chuyển
        DB::table('storage_blocks')->insert([
            'warehouse_id' => $whT1,
            'block_code' => 'T1-IN',
            'total_slots' => 50,
            'status' => 'available',
            'created_at' => now(), 
            'updated_at' => now()
        ]);
        
        DB::table('storage_blocks')->insert([
            'warehouse_id' => $whT2,
            'block_code' => 'T2-OUT',
            'total_slots' => 50,
            'status' => 'available',
            'created_at' => now(), 
            'updated_at' => now()
        ]);
    }
}