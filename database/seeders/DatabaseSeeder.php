<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class, // Tạo User Admin và Quyền
            AddressSeeder::class,        // Tạo Tỉnh, Huyện, Xã
            ProductSeeder::class,        // Tạo Danh mục, Brand, Sản phẩm
            SupplierSeeder::class,       // Tạo NCC và map Sản phẩm
            WarehouseSeeder::class, 
            UserSeeder::class,          // Tạo User Admin và 50 user thường
        ]);

        echo "Database seeded successfully with FULL business logic & dummy data!\n";
    }
}
