<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InboundSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Đồng bộ lại Sequence của Postgres trước khi chạy để tránh lỗi ID cũ còn sót
        $this->fixPostgresSequence('product_batches');
        $this->fixPostgresSequence('inbound_orders');
        $this->fixPostgresSequence('inbound_items');
        $this->fixPostgresSequence('inventory');
        $this->fixPostgresSequence('inventory_transactions');

        // Lấy danh sách ID để random
        $suppliers = DB::table('suppliers')->pluck('id')->toArray();
        $staffs = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->whereIn('roles.name', ['admin', 'staff'])
            ->pluck('model_has_roles.model_id')->toArray();
        
        // SỬA TẠI ĐÂY: Lấy mảng object chứa cả id lẫn price thay vì pluck trùng key price
        $products = DB::table('products')->select('id', 'price')->get()->toArray();
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
            
            for ($j = 0; $j < $numItems; $j++) {
                // SỬA TẠI ĐÂY: Bốc ngẫu nhiên nguyên Object Product để tránh lệch ID và Price
                $randomProduct = $products[array_rand($products)];
                $productId = $randomProduct->id;
                $price = $randomProduct->price;

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
                    'quantity_change' => $quantity,
                    'balance_after' => $balanceAfter,
                    'staff_id' => $staffId,
                    'note' => 'Nhập hàng từ phiếu INB-' . $inboundId,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }
    }

    /**
     * Hàm hỗ trợ tự động đồng bộ lại chuỗi tự tăng (Sequence) trên PostgreSQL
     */
    private function fixPostgresSequence(string $tableName): void
    {
        if (config('database.default') === 'pgsql') {
            DB::statement("SELECT setval(pg_get_serial_sequence('{$tableName}', 'id'), coalesce(max(id), 0) + 1, false) FROM {$tableName};");
        }
    }
}