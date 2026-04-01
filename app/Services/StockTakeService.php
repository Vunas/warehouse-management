<?php

namespace App\Services;

use App\Models\StockTake;
use App\Models\StockTakeItem;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Exception;

class StockTakeService
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function createDraft(array $data)
    {
        // Tạo mã tự động
        $latest = StockTake::latest('id')->first();
        $nextId = $latest ? $latest->id + 1 : 1;
        $data['code'] = 'KK-' . date('Ym') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        
        $data['status'] = 'draft';
        return StockTake::create($data);
    }

    public function startCounting($id)
    {
        $stockTake = StockTake::findOrFail($id);

        if ($stockTake->status !== 'draft') {
            throw new Exception("Phiếu chỉ được bắt đầu khi đang ở trạng thái Nháp.");
        }

        return DB::transaction(function () use ($stockTake) {
            // Cập nhật trạng thái
            $stockTake->update([
                'status' => 'counting',
                'started_at' => now(),
            ]);

            // Chụp ảnh tồn kho hiện tại (Snapshot) của Nhà kho này
            $inventories = Inventory::whereHas('location', function($q) use ($stockTake) {
                $q->where('warehouse_id', $stockTake->warehouse_id);
            })->where('quantity', '>', 0)->get();

            // Insert hàng loạt vào chi tiết phiếu kiểm kê
            $itemsData = [];
            foreach ($inventories as $inv) {
                $itemsData[] = [
                    'stock_take_id'     => $stockTake->id,
                    'product_id'        => $inv->product_id,
                    'location_id'       => $inv->location_id,
                    'batch_id'          => $inv->batch_id,
                    'expected_quantity' => $inv->quantity,
                    'counted_quantity'  => null,
                    'variance'          => null,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ];
            }

            if (!empty($itemsData)) {
                StockTakeItem::insert($itemsData);
            }

            return $stockTake;
        });
    }

    public function saveCountBulk($id, array $itemsData)
    {
        $stockTake = StockTake::findOrFail($id);
        
        if ($stockTake->status !== 'counting') {
            throw new Exception("Không thể lưu vì phiếu không trong trạng thái đang đếm.");
        }

        return DB::transaction(function () use ($stockTake, $itemsData) {
            foreach ($itemsData as $itemId => $data) {
                $item = StockTakeItem::where('stock_take_id', $stockTake->id)->find($itemId);
                if (!$item) continue;

                $counted = $data['counted_quantity'];
                
                if (is_numeric($counted)) {
                    $item->update([
                        'counted_quantity' => $counted,
                        'variance'         => $counted - $item->expected_quantity,
                        'reason'           => $data['reason'] ?? null
                    ]);
                }
            }
            return true;
        });
    }

    public function complete($id, $staffId)
    {
        $stockTake = StockTake::with('items')->findOrFail($id);

        if ($stockTake->status !== 'counting') {
            throw new Exception("Phiếu phải ở trạng thái đang kiểm kê mới có thể hoàn tất.");
        }

        return DB::transaction(function () use ($stockTake, $staffId) {
            foreach ($stockTake->items as $item) {
                if ($item->counted_quantity === null) {
                    throw new Exception("Sản phẩm SP-{$item->product_id} chưa được nhập số lượng đếm thực tế.");
                }

                if ($item->variance != 0) {
                    if (empty($item->reason)) {
                        throw new Exception("Vui lòng nhập lý do cho SP-{$item->product_id} do có sự chênh lệch (Cảnh báo: Lệch {$item->variance}).");
                    }

                    $this->inventoryService->adjustStock(
                        $item->product_id,
                        $item->location_id,
                        $item->batch_id,
                        $item->variance,
                        "Kiểm kê kho (Phiếu {$stockTake->code}): " . $item->reason,
                        $staffId,
                        $stockTake->id
                    );
                }
            }

            $stockTake->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            return $stockTake;
        });
    }

    public function cancel($id)
    {
        $stockTake = StockTake::findOrFail($id);
        if (in_array($stockTake->status, ['completed', 'cancelled'])) {
            throw new Exception("Không thể hủy phiếu này.");
        }
        $stockTake->update(['status' => 'cancelled']);
        return $stockTake;
    }
}