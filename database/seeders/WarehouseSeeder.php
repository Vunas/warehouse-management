<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        /*
        |--------------------------------------------------------------------------
        | 1. WAREHOUSES
        |--------------------------------------------------------------------------
        */
        DB::table('warehouses')->insert([
            ['id' => 1, 'name' => 'Kho Tổng Miền Nam', 'location' => 'KCN Tân Bình, TP HCM', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Kho Miền Bắc', 'location' => 'KCN Bắc Thăng Long, Hà Nội', 'created_at' => $now, 'updated_at' => $now],
        ]);

        /*
        |--------------------------------------------------------------------------
        | 2. LOCATIONS (ZONE → SHELF)
        |--------------------------------------------------------------------------
        */
        $locations = [
            // ===== WAREHOUSE 1 =====
            ['id' => 1, 'warehouse_id' => 1, 'parent_id' => null, 'name' => 'ZONE-A (Hàng giá trị cao)', 'type' => 'zone', 'is_store' => false, 'picking_priority' => 1],
            ['id' => 2, 'warehouse_id' => 1, 'parent_id' => null, 'name' => 'ZONE-B (Hàng phổ thông)', 'type' => 'zone', 'is_store' => false, 'picking_priority' => 2],

            // Shelf Zone A
            ['id' => 3, 'warehouse_id' => 1, 'parent_id' => 1, 'name' => 'A-01-01', 'type' => 'shelf', 'is_store' => true, 'picking_priority' => 1],
            ['id' => 4, 'warehouse_id' => 1, 'parent_id' => 1, 'name' => 'A-01-02', 'type' => 'shelf', 'is_store' => true, 'picking_priority' => 2],
            ['id' => 5, 'warehouse_id' => 1, 'parent_id' => 1, 'name' => 'A-02-01', 'type' => 'shelf', 'is_store' => true, 'picking_priority' => 3],

            // Shelf Zone B
            ['id' => 6, 'warehouse_id' => 1, 'parent_id' => 2, 'name' => 'B-01-01', 'type' => 'shelf', 'is_store' => true, 'picking_priority' => 1],
            ['id' => 7, 'warehouse_id' => 1, 'parent_id' => 2, 'name' => 'B-01-02', 'type' => 'shelf', 'is_store' => true, 'picking_priority' => 2],

            // ===== WAREHOUSE 2 =====
            ['id' => 8, 'warehouse_id' => 2, 'parent_id' => null, 'name' => 'ZONE-C (Hàng lạnh)', 'type' => 'zone', 'is_store' => false, 'picking_priority' => 1],
            ['id' => 9, 'warehouse_id' => 2, 'parent_id' => null, 'name' => 'ZONE-D (Hàng nặng)', 'type' => 'zone', 'is_store' => false, 'picking_priority' => 2],

            // Shelf Zone C
            ['id' => 10, 'warehouse_id' => 2, 'parent_id' => 8, 'name' => 'C-01-01', 'type' => 'shelf', 'is_store' => true, 'picking_priority' => 1],
            ['id' => 11, 'warehouse_id' => 2, 'parent_id' => 8, 'name' => 'C-01-02', 'type' => 'shelf', 'is_store' => true, 'picking_priority' => 2],

            // Shelf Zone D
            ['id' => 12, 'warehouse_id' => 2, 'parent_id' => 9, 'name' => 'D-01-01', 'type' => 'shelf', 'is_store' => true, 'picking_priority' => 1],
            ['id' => 13, 'warehouse_id' => 2, 'parent_id' => 9, 'name' => 'D-01-02', 'type' => 'shelf', 'is_store' => true, 'picking_priority' => 2],
        ];

        foreach ($locations as &$loc) {
            $loc['created_at'] = $now;
            $loc['updated_at'] = $now;
        }

        DB::table('locations')->insert($locations);

        /*
        |--------------------------------------------------------------------------
        | 3. PRODUCT BATCHES
        |--------------------------------------------------------------------------
        */
        DB::table('product_batches')->insert([
            [
                'id' => 1,
                'product_id' => 1,
                'batch_code' => 'BATCH-001',
                'expiry_date' => '2027-01-22',
                'manufacture_date' => '2026-01-01',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 2,
                'product_id' => 2,
                'batch_code' => 'BATCH-002',
                'expiry_date' => '2026-10-01',
                'manufacture_date' => '2026-02-01',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | 4. INVENTORY (PHÂN TÁN NHIỀU KHO)
        |--------------------------------------------------------------------------
        */
        DB::table('inventory')->insert([
            [
                'product_id' => 1,
                'location_id' => 3, // Kho Nam - A-01-01
                'batch_id' => 1,
                'quantity' => 100,
                'reserved_quantity' => 10,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'product_id' => 1,
                'location_id' => 10, // Kho Bắc - C-01-01
                'batch_id' => 1,
                'quantity' => 50,
                'reserved_quantity' => 5,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'product_id' => 2,
                'location_id' => 6, // Kho Nam - B-01-01
                'batch_id' => 2,
                'quantity' => 80,
                'reserved_quantity' => 0,
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
