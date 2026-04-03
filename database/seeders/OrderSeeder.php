<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customers = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'customer')
            ->pluck('model_has_roles.model_id')->toArray();
            
        $productsArr = DB::table('products')->get()->toArray();
        
        if (empty($customers) || empty($productsArr)) {
            return;
        }

        // Đảm bảo các user này có địa chỉ
        $wards = DB::table('wards')->pluck('id')->toArray();
        foreach ($customers as $customerId) {
            $hasAddress = DB::table('addresses')->where('user_id', $customerId)->exists();
            if (!$hasAddress && !empty($wards)) {
                DB::table('addresses')->insert([
                    'user_id' => $customerId,
                    'ward_id' => $wards[array_rand($wards)],
                    'detail' => 'Số ' . rand(1, 100) . ' Đường Nguyễn Văn Linh',
                    'is_default' => true,
                    'created_at' => Carbon::now(),
                ]);
            }
        }

        // Tạo 30 đơn hàng ngẫu nhiên trong 30 ngày qua
        for ($i = 1; $i <= 30; $i++) {
            $customerId = $customers[array_rand($customers)];
            $addressId = DB::table('addresses')->where('user_id', $customerId)->value('id');
            $createdAt = Carbon::now()->subDays(rand(1, 30))->subHours(rand(1, 24));
            
            // Random trạng thái đơn hàng
            $statuses = ['completed', 'completed', 'completed', 'paid', 'shipping', 'cancelled'];
            $status = $statuses[array_rand($statuses)];

            // 1. Tạo Order
            $orderId = DB::table('orders')->insertGetId([
                'user_id' => $customerId,
                'address_id' => $addressId,
                'total_price' => 0, // Sẽ update sau
                'status' => $status,
                'customer_note' => rand(0, 1) ? 'Giao giờ hành chính giúp mình' : null,
                'order_date' => $createdAt,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $numItems = rand(1, 4);
            $totalPrice = 0;

            // 2. Tạo Order Items
            for ($j = 0; $j < $numItems; $j++) {
                $product = $productsArr[array_rand($productsArr)];
                $qty = rand(1, 3);
                $price = $product->price;
                
                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $price,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
                
                $totalPrice += ($qty * $price);
            }

            // Cập nhật tổng tiền đơn hàng
            DB::table('orders')->where('id', $orderId)->update(['total_price' => $totalPrice]);

            // 3. Tạo dữ liệu thanh toán (Payments)
            $paymentMethods = ['vnpay', 'bank_transfer', 'cash', 'momo'];
            $paymentStatus = in_array($status, ['completed', 'paid', 'shipping']) ? 'paid' : 'pending';
            if ($status === 'cancelled') $paymentStatus = 'failed';

            DB::table('payments')->insert([
                'order_id' => $orderId,
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'transaction_id' => 'TXN' . time() . rand(100, 999),
                'amount' => $totalPrice,
                'status' => $paymentStatus,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }
}