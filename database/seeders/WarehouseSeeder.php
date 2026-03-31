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

        DB::table('warehouses')->insert([
            ['id' => 1, 'name' => 'Kho Tổng Miền Nam', 'location' => 'KCN Tân Bình, TP HCM', 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('locations')->insert([
            ['id' => 1, 'warehouse_id' => 1, 'parent_id' => null, 'name' => 'Khu A - Hàng giá trị cao', 'type' => 'zone', 'is_store' => false, 'picking_priority' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'warehouse_id' => 1, 'parent_id' => null, 'name' => 'Khu B - Hàng phổ thông', 'type' => 'zone', 'is_store' => false, 'picking_priority' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'warehouse_id' => 1, 'parent_id' => 1, 'name' => 'A-01', 'type' => 'shelf', 'is_store' => true, 'picking_priority' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'warehouse_id' => 1, 'parent_id' => 1, 'name' => 'A-02', 'type' => 'shelf', 'is_store' => true, 'picking_priority' => 2, 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('product_batches')->insert([
            [
                'id' => 1,
                'product_id' => 1,
                'batch_code' => 'BATCH-001',
                'expiry_date' => '2026-12-31',
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

        DB::table('inventory')->insert([
            [
                'product_id' => 1,
                'location_id' => 3,
                'batch_id' => 1,
                'quantity' => 100,
                'reserved_quantity' => 0,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'product_id' => 2,
                'location_id' => 4,
                'batch_id' => 2,
                'quantity' => 50,
                'reserved_quantity' => 0,
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
