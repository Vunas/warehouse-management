<?php

namespace App\Services;

use App\Models\OutboundOrder;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Exception;

class OutboundOrderService
{
    // API Lấy tồn kho cho AJAX (Đã thêm Batch - Lô hàng và fix lỗi SQL)
    public function getInventoryForDropdown($warehouseId)
    {
        return Inventory::with(['product', 'location', 'batch'])
            ->whereHas('location', function ($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            })
            // Ghi rõ inventory.quantity để tránh lỗi ambiguous (mơ hồ)
            ->whereRaw('(inventory.quantity - inventory.reserved_quantity) > 0') 
            ->get()
            ->sortBy(function($inventory) {
                // Sắp xếp FEFO: Ưu tiên lô hàng cận date lên đầu
                return optional($inventory->batch)->expiry_date ?: '9999-12-31';
            })
            ->values(); // Reset lại index mảng cho Javascript dễ đọc
    }

    public function createOutboundOrder(array $data, array $items)
    {
        $data['status'] = 'pending'; 

        return DB::transaction(function () use ($data, $items) {
            $outbound = OutboundOrder::create($data);

            foreach ($items as $item) {
                if (!empty($item['product_id']) && !empty($item['location_id']) && !empty($item['quantity'])) {
                    
                    $batchId = !empty($item['batch_id']) ? $item['batch_id'] : null;

                    // KIỂM TRA TỒN KHO THỰC TẾ
                    $query = Inventory::where('product_id', $item['product_id'])
                        ->where('location_id', $item['location_id']);
                    
                    if ($batchId) {
                        $query->where('batch_id', $batchId);
                    } else {
                        $query->whereNull('batch_id');
                    }
                        
                    $inventory = $query->first();

                    $available = $inventory ? ($inventory->quantity - $inventory->reserved_quantity) : 0;
                    if ($item['quantity'] > $available) {
                        throw new Exception("Sản phẩm ID {$item['product_id']} tại vị trí ID {$item['location_id']} không đủ hàng tồn (Chỉ còn {$available}).");
                    }

                    $outbound->items()->create([
                        'product_id'  => $item['product_id'],
                        'location_id' => $item['location_id'],
                        'batch_id'    => $batchId,
                        'quantity'    => $item['quantity'],
                    ]);
                }
            }
            return $outbound;
        });
    }

    public function updateOutboundOrder($id, array $data, array $items)
    {
        $outbound = OutboundOrder::findOrFail($id);
        if ($outbound->status !== 'pending') {
            throw new Exception("Chỉ được phép sửa phiếu đang chờ xuất.");
        }

        return DB::transaction(function () use ($outbound, $data, $items) {
            $outbound->update($data);
            $outbound->items()->delete(); // Xóa chi tiết cũ

            foreach ($items as $item) {
                if (!empty($item['product_id']) && !empty($item['location_id']) && !empty($item['quantity'])) {
                    $outbound->items()->create([
                        'product_id'  => $item['product_id'],
                        'location_id' => $item['location_id'],
                        'batch_id'    => !empty($item['batch_id']) ? $item['batch_id'] : null,
                        'quantity'    => $item['quantity'],
                    ]);
                }
            }
            return $outbound;
        });
    }

    public function completeOutboundOrder($outboundId)
    {
        $outbound = OutboundOrder::with('items')->findOrFail($outboundId);

        if ($outbound->status !== 'pending') {
            throw new Exception("Phiếu xuất kho này đã được xử lý.");
        }

        return DB::transaction(function () use ($outbound) {
            $outbound->update(['status' => 'completed']);
            $isSales = ($outbound->type === 'sales');

            foreach ($outbound->items as $item) {
                // TRỪ KHO ĐÍCH DANH (Dựa vào Product + Location + Batch)
                $query = Inventory::where('product_id', $item->product_id)
                    ->where('location_id', $item->location_id);
                
                if ($item->batch_id) {
                    $query->where('batch_id', $item->batch_id);
                } else {
                    $query->whereNull('batch_id');
                }

                $inventory = $query->first();

                if (!$inventory || $inventory->quantity < $item->quantity) {
                    throw new Exception("Lỗi: Vị trí lấy hàng không đủ số lượng để trừ!");
                }

                $updateData = ['quantity' => $inventory->quantity - $item->quantity];
                if ($isSales) {
                    $updateData['reserved_quantity'] = $inventory->reserved_quantity - $item->quantity;
                }

                $inventory->update($updateData);
            }

            if ($isSales && $outbound->order_id) {
                \App\Models\Order::where('id', $outbound->order_id)->update(['status' => 'shipping']);
            }

            return $outbound;
        });
    }

    public function cancelOutboundOrder($outboundId)
    {
        $outbound = OutboundOrder::findOrFail($outboundId);
        $outbound->update(['status' => 'cancelled']);
        return $outbound;
    }
}