<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        
        $existingAdmin = DB::table('users')->where('username', 'admin')->first();

        if (!$existingAdmin) {
            
            $adminId = DB::table('users')->insertGetId([
                'username' => 'admin',
                'password' => Hash::make('password'), 
                'full_name' => 'System Administrator',
                'email' => 'admin@warehouse.com',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            
            $empId = DB::table('employees')->insertGetId([
                'user_id' => $adminId,
                'position' => 'System Admin',
                'hired_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            
            $adminRole = DB::table('roles')->where('name', 'Admin')->first();

            
            if ($adminRole) {
                DB::table('employee_role')->insert([
                    'employee_id' => $empId,
                    'role_id' => $adminRole->id
                ]);
            }
        }
    }
}