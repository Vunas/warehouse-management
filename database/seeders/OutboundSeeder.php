<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OutboundSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy các đơn hàng đã 'completed' hoặc 'shipping' để tạo phiếu xuất kho
        $orders = DB::table('orders')
            ->whereIn('status', ['completed', 'shipping'])
            ->get();

        $staffs = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->whereIn('roles.name', ['admin', 'staff'])
            ->pluck('model_has_roles.model_id')->toArray();
            
        $warehouseId = DB::table('warehouses')->value('id');

        if ($orders->isEmpty() || empty($staffs) || !$warehouseId) {
            return;
        }

        foreach ($orders as $order) {
            $staffId = $staffs[array_rand($staffs)];
            $createdAt = Carbon::parse($order->created_at)->addHours(2); // Xuất kho sau khi đặt hàng 2 tiếng

            // 1. Tạo Outbound Order
            $outboundId = DB::table('outbound_orders')->insertGetId([
                'order_id' => $order->id,
                'staff_id' => $staffId,
                'warehouse_id' => $warehouseId,
                'type' => 'sales',
                'status' => 'completed',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Lấy các sản phẩm trong đơn hàng
            $orderItems = DB::table('order_items')->where('order_id', $order->id)->get();

            foreach ($orderItems as $item) {
                // Tìm dòng tồn kho có sẵn của sản phẩm này (ưu tiên lấy dòng có số lượng > 0)
                $inventory = DB::table('inventory')
                    ->where('product_id', $item->product_id)
                    ->where('quantity', '>=', $item->quantity)
                    ->first();

                // Nếu không đủ tồn kho ở 1 location, tạm thời cho phép xuất âm hoặc lấy đại (vì đây là dummy data)
                if (!$inventory) {
                    $inventory = DB::table('inventory')->where('product_id', $item->product_id)->first();
                }

                if ($inventory) {
                    // 2. Tạo Outbound Item
                    DB::table('outbound_items')->insert([
                        'outbound_id' => $outboundId,
                        'product_id' => $item->product_id,
                        'batch_id' => $inventory->batch_id,
                        'location_id' => $inventory->location_id,
                        'quantity' => $item->quantity,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);

                    // 3. Trừ tồn kho
                    DB::table('inventory')->where('id', $inventory->id)->decrement('quantity', $item->quantity);
                    
                    // Tính lại balance_after
                    $currentQty = DB::table('inventory')->where('id', $inventory->id)->value('quantity');

                    // 4. Ghi Sổ Kho (Inventory Transaction)
                    DB::table('inventory_transactions')->insert([
                        'product_id' => $item->product_id,
                        'location_id' => $inventory->location_id,
                        'batch_id' => $inventory->batch_id,
                        'transaction_type' => 'outbound',
                        'reference_id' => $outboundId,
                        'quantity_change' => -$item->quantity, // Số âm vì là xuất kho
                        'balance_after' => $currentQty,
                        'staff_id' => $staffId,
                        'note' => 'Xuất bán hàng cho đơn SO-' . $order->id,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);
                }
            }
        }
    }
}