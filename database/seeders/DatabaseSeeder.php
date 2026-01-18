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
            UserSeeder::class,  
            PermissionSeeder::class,
        ]);

        echo "Database seeded successfully with new structure!\n";
    }
}
