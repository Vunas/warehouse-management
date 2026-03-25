<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Suppliers (Nhà cung cấp)
        DB::table('suppliers')->insert([
            [
                'id' => 1,
                'name' => 'Công ty Phụ tùng Ô tô Minh Phát',
                'phone' => '0901234567',
                'email' => 'minhphat@autoparts.vn',
                'address' => 'Quận 12, TP.HCM',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 2,
                'name' => 'Auto Parts Sài Gòn',
                'phone' => '0912345678',
                'email' => 'contact@autosaigon.vn',
                'address' => 'Thủ Đức, TP.HCM',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);

        // 2. Supplier - Product mapping
        DB::table('supplier_products')->insert([
            ['supplier_id' => 1, 'product_id' => 1],
            ['supplier_id' => 1, 'product_id' => 2],
            ['supplier_id' => 2, 'product_id' => 3],
            ['supplier_id' => 2, 'product_id' => 4],
        ]);
    }
}
