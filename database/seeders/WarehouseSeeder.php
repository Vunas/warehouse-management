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

        // Warehouse
        DB::table('warehouses')->insert([
            ['id' => 1, 'name' => 'Kho Tổng Miền Nam', 'location' => 'KCN Tân Bình, TP HCM', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Locations (zone + shelf)
        DB::table('locations')->insert([
            ['id' => 1, 'warehouse_id' => 1, 'parent_id' => null, 'name' => 'Khu A - Hàng giá trị cao', 'type' => 'zone', 'is_store' => false, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'warehouse_id' => 1, 'parent_id' => null, 'name' => 'Khu B - Hàng phổ thông', 'type' => 'zone', 'is_store' => false, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'warehouse_id' => 1, 'parent_id' => 1, 'name' => 'A-01', 'type' => 'shelf', 'is_store' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'warehouse_id' => 1, 'parent_id' => 1, 'name' => 'A-02', 'type' => 'shelf', 'is_store' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Inventory
        DB::table('inventory')->insert([
            ['product_id' => 1, 'location_id' => 3, 'quantity' => 100, 'created_at' => $now, 'updated_at' => $now],
            ['product_id' => 2, 'location_id' => 4, 'quantity' => 50, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
