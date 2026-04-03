<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InboundSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy danh sách ID để random
        $suppliers = DB::table('suppliers')->pluck('id')->toArray();
        $staffs = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->whereIn('roles.name', ['admin', 'staff'])
            ->pluck('model_has_roles.model_id')->toArray();
        $products = DB::table('products')->pluck('id', 'price')->toArray();
        $locations = DB::table('locations')->where('is_store', true)->pluck('id')->toArray();

        if (empty($suppliers) || empty($staffs) || empty($products) || empty($locations)) {
            return;
        }

        // Tạo 10 phiếu nhập kho trong 3 tháng qua
        for ($i = 1; $i <= 10; $i++) {
            $createdAt = Carbon::now()->subDays(rand(1, 90));
            $staffId = $staffs[array_rand($staffs)];

            // 1. Tạo phiếu Inbound
            $inboundId = DB::table('inbound_orders')->insertGetId([
                'supplier_id' => $suppliers[array_rand($suppliers)],
                'staff_id' => $staffId,
                'status' => 'completed',
                'notes' => 'Nhập kho định kỳ đợt ' . $i,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Mỗi phiếu nhập 2-5 mặt hàng
            $numItems = rand(2, 5);
            $productPrices = array_keys($products);
            
            for ($j = 0; $j < $numItems; $j++) {
                $price = $productPrices[array_rand($productPrices)];
                $productId = $products[$price];
                $quantity = rand(10, 50);
                $locationId = $locations[array_rand($locations)];

                // Tạo Lô hàng (Batch) mới
                $batchId = DB::table('product_batches')->insertGetId([
                    'product_id' => $productId,
                    'batch_code' => 'INB-' . $createdAt->format('Ymd') . '-' . rand(100, 999),
                    'manufacture_date' => $createdAt->copy()->subMonths(1),
                    'expiry_date' => $createdAt->copy()->addYears(2),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                // 2. Tạo chi tiết phiếu nhập
                DB::table('inbound_items')->insert([
                    'inbound_id' => $inboundId,
                    'product_id' => $productId,
                    'batch_id' => $batchId,
                    'location_id' => $locationId,
                    'quantity' => $quantity,
                    'price' => $price * 0.7, // Giá nhập = 70% giá bán
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                // 3. Cộng tồn kho (Inventory)
                $inventory = DB::table('inventory')
                    ->where('product_id', $productId)
                    ->where('location_id', $locationId)
                    ->where('batch_id', $batchId)
                    ->first();

                $balanceAfter = $quantity;

                if ($inventory) {
                    DB::table('inventory')->where('id', $inventory->id)->increment('quantity', $quantity);
                    $balanceAfter += $inventory->quantity;
                } else {
                    DB::table('inventory')->insert([
                        'product_id' => $productId,
                        'location_id' => $locationId,
                        'batch_id' => $batchId,
                        'quantity' => $quantity,
                        'reserved_quantity' => 0,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);
                }

                // 4. Ghi Sổ Kho (Inventory Transaction)
                DB::table('inventory_transactions')->insert([
                    'product_id' => $productId,
                    'location_id' => $locationId,
                    'batch_id' => $batchId,
                    'transaction_type' => 'inbound',
                    'reference_id' => $inboundId,
                    'quantity_change' => $quantity, // Số dương vì là nhập kho
                    'balance_after' => $balanceAfter,
                    'staff_id' => $staffId,
                    'note' => 'Nhập hàng từ phiếu INB-' . $inboundId,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }
    }
}