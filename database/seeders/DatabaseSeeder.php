<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class, 
            AddressSeeder::class,        
            ProductSeeder::class,        
            SupplierSeeder::class,       
            WarehouseSeeder::class, 
            UserSeeder::class,          
            ProductAlertSeeder::class,
        ]);

        echo "Database seeded successfully with FULL business logic & dummy data!\n";
    }
}
