<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            WarehouseTypeSeeder::class,
            WarehouseSeeder::class,
            SizeRuleSeeder::class,
            CategorySeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,
            EmployeeSeeder::class,
            ProductSeeder::class,
            CustomerSeeder::class,
            ContractSeeder::class,
            InventorySeeder::class,
        ]);

        echo "Database seeded successfully with FULL business logic & dummy data!\n";
    }
}
