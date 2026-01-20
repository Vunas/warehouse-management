<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $userA = User::firstOrCreate(
            ['username' => 'customer_a'],
            [
                'password' => Hash::make('123456'),
                'full_name' => 'Nguyen Van A (Construction Co.)',
                'email' => 'clientA@construction.com',
                'is_active' => true
            ]
        );

        Customer::updateOrCreate(
            ['user_id' => $userA->id],
            [
                'company_name' => 'Construction Co. Ltd',
                'tax_code' => '0101234567',
                'billing_phone' => '0909123456',
                'address' => 'Hà Nội'
            ]
        );

        $userB = User::firstOrCreate(
            ['username' => 'customer_b'],
            [
                'password' => Hash::make('123456'),
                'full_name' => 'Tran Thi B (Fashion Corp)',
                'email' => 'clientB@fashion.com',
                'is_active' => true
            ]
        );

        Customer::updateOrCreate(
            ['user_id' => $userB->id],
            [
                'company_name' => 'Fashion Corp',
                'tax_code' => '0109876543',
                'billing_phone' => '0909888777',
                'address' => 'Hồ Chí Minh'
            ]
        );

        Customer::factory()->count(18)->create();

        echo "Seeded: 2 Fixed Customers & 18 Random Customers.\n";
    }
}