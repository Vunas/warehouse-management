<?php

namespace App\Services;

use App\Repositories\Interfaces\InventoryRepositoryInterface;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Location;
use App\Events\InventoryTransactionRecorded;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;

class InventoryService
{
    protected $inventoryRepo;

    public function __construct(InventoryRepositoryInterface $inventoryRepo)
    {
        $this->inventoryRepo = $inventoryRepo;
    }

    public function getPaginatedInventories(int $perPage, array $filters = [])
    {
        // Truyền filters xuống Repository. (Xem hướng dẫn cập nhật Repository ở cuối)
        return $this->inventoryRepo->getPaginated($perPage, $filters);
    }

    public function getInventoryById($id)
    {
        return $this->inventoryRepo->findById($id);
    }

    public function reserveStock(int $productId, int $quantityToReserve)
    {
        return DB::transaction(function () use ($productId, $quantityToReserve) {
            // Lấy tồn kho sắp xếp chuẩn FEFO -> FIFO
            $inventories = $this->inventoryRepo->getPrioritizedStock($productId);
            $remaining = $quantityToReserve;

            foreach ($inventories as $inv) {
                if ($remaining <= 0) break;

                $currentInv = $this->inventoryRepo->getLockedById($inv->id);
                $available = $currentInv->quantity - $currentInv->reserved_quantity;

                if ($available <= 0) continue;

                $reserveAmount = min($available, $remaining);

                $this->inventoryRepo->update($currentInv->id, [
                    'reserved_quantity' => $currentInv->reserved_quantity + $reserveAmount
                ]);

                $remaining -= $reserveAmount;
            }

            if ($remaining > 0) {
                throw new Exception("Sản phẩm ID {$productId} không đủ tồn kho khả dụng.");
            }
        });
    }

    /**
     * HỦY ĐƠN HÀNG -> NHẢ TỒN KHO GIỮ CHỖ (RELEASE RESERVED STOCK)
     * Giảm reserved_quantity, KHÔNG ảnh hưởng quantity (on_hand)
     */
    public function releaseReservedStock(int $productId, int $quantityToRelease)
    {
        return DB::transaction(function () use ($productId, $quantityToRelease) {
            $inventories = $this->inventoryRepo->getReservedStockByProduct($productId);
            $remaining = $quantityToRelease;

            foreach ($inventories as $inv) {
                if ($remaining <= 0) break;

                if ($inv->reserved_quantity <= 0) continue;

                $releaseAmount = min($inv->reserved_quantity, $remaining);

                $this->inventoryRepo->update($inv->id, [
                    'reserved_quantity' => $inv->reserved_quantity - $releaseAmount
                ]);

                $remaining -= $releaseAmount;
            }
        });
    }

    /**
     * BƯỚC 3: XÁC NHẬN XUẤT KHO (PICKING CONFIRMED) -> TRỪ KHO THỰC TẾ
     * Giảm CẢ quantity (on_hand) LẪN reserved_quantity
     */
    public function deductExactStock(int $productId, int $locationId, ?int $batchId, int $quantityToDeduct, bool $isSales = true, ?int $referenceId = null, string $note = '')
    {
        $inventory = $this->inventoryRepo->getLockedStock($productId, $locationId, $batchId);

        if (!$inventory || $inventory->quantity < $quantityToDeduct) {
            throw new Exception("Sản phẩm ID {$productId} tại vị trí ID {$locationId} không đủ số lượng tồn thực tế để trừ!");
        }

        if ($isSales && $inventory->reserved_quantity < $quantityToDeduct) {
            throw new Exception("Lỗi đồng bộ: Số lượng hàng giữ chỗ (reserved) ít hơn số lượng thực xuất cho đơn bán hàng.");
        }

        $updateData = ['quantity' => $inventory->quantity - $quantityToDeduct];
        if ($isSales) {
            // Trừ đi lượng đã giữ chỗ vì hàng đã thực sự rời kho
            $updateData['reserved_quantity'] = $inventory->reserved_quantity - $quantityToDeduct;
        }

        $this->inventoryRepo->update($inventory->id, $updateData);

        InventoryTransactionRecorded::dispatch(array_merge([
            'product_id'       => $productId,
            'location_id'      => $locationId,
            'batch_id'         => $batchId,
            'transaction_type' => 'outbound',
            'reference_id'     => $referenceId,
            'quantity_change'  => -$quantityToDeduct,
            'balance_after'    => $updateData['quantity'],
            'staff_id'         => Auth::id() ?? null,
            'note'             => $note ?: ($isSales ? 'Xuất kho bán hàng' : 'Xuất kho nội bộ/điều chỉnh')
        ]));

        return $inventory;
    }

    public function getInventoryStatistics($inventory)
    {
        // Lần cuối cùng nhập kho (inbound)
        $lastImport = DB::table('inventory_transactions')
            ->where('product_id', $inventory->product_id)
            ->where('location_id', $inventory->location_id)
            ->where('batch_id', $inventory->batch_id)
            ->where('transaction_type', 'inbound')
            ->orderBy('created_at', 'desc')
            ->first();

        // Tổng số lượng đã nhập
        $totalImported = DB::table('inventory_transactions')
            ->where('product_id', $inventory->product_id)
            ->where('location_id', $inventory->location_id)
            ->where('batch_id', $inventory->batch_id)
            ->where('transaction_type', 'inbound')
            ->sum('quantity_change');

        // Tổng số lượng đã xuất (outbound thường lưu số âm, nên dùng abs để lấy số tuyệt đối)
        $totalExported = DB::table('inventory_transactions')
            ->where('product_id', $inventory->product_id)
            ->where('location_id', $inventory->location_id)
            ->where('batch_id', $inventory->batch_id)
            ->where('transaction_type', 'outbound')
            ->sum('quantity_change');

        return [
            'last_import_date' => $lastImport ? $lastImport->created_at : null,
            'total_imported'   => $totalImported,
            'total_exported'   => abs($totalExported),
        ];
    }

    public function getFormData()
    {
        return [
            'warehouses' => Warehouse::all(),
            'products'   => Product::all(),
            'batches'    => ProductBatch::all()
        ];
    }

    public function getStoreLocations(int $warehouseId)
    {
        return Location::where('warehouse_id', $warehouseId)
            ->where('is_store', true)
            ->get();
    }

    public function addStock(array $data)
    {
        return DB::transaction(function () use ($data) {
            $inventory = $this->inventoryRepo->getLockedStock($data['product_id'], $data['location_id'], $data['batch_id'] ?? null);
            $quantityChange = $data['quantity'];

            if ($inventory) {
                $balanceAfter = $inventory->quantity + $quantityChange;
                $inventory = $this->inventoryRepo->update($inventory->id, [
                    'quantity' => $balanceAfter
                ]);
            } else {
                $balanceAfter = $quantityChange;
                $inventory = $this->inventoryRepo->create([
                    'product_id'        => $data['product_id'],
                    'location_id'       => $data['location_id'],
                    'batch_id'          => $data['batch_id'] ?? null,
                    'quantity'          => $balanceAfter,
                    'reserved_quantity' => 0
                ]);
            }

            InventoryTransactionRecorded::dispatch([
                'product_id'       => $data['product_id'],
                'location_id'      => $data['location_id'],
                'batch_id'         => $data['batch_id'] ?? null,
                'transaction_type' => 'inbound',
                'reference_id'     => $data['reference_id'] ?? null,
                'quantity_change'  => $quantityChange,
                'balance_after'    => $balanceAfter,
                'staff_id'         => Auth::id() ?? null,
                'note'             => $data['note'] ?? 'Thêm / Cộng dồn tồn kho'
            ]);

            return $inventory;
        });
    }

    public function updateStock($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $inventory = $this->inventoryRepo->findById($id);
            $oldQuantity = $inventory->quantity;
            $newQuantity = $data['quantity'];
            $variance = $newQuantity - $oldQuantity;

            $inventory = $this->inventoryRepo->update($id, [
                'quantity' => $newQuantity
            ]);

            if ($variance != 0) {
                InventoryTransactionRecorded::dispatch([
                    'product_id'       => $inventory->product_id,
                    'location_id'      => $inventory->location_id,
                    'batch_id'         => $inventory->batch_id,
                    'transaction_type' => 'adjustment',
                    'reference_id'     => null,
                    'quantity_change'  => $variance,
                    'balance_after'    => $newQuantity,
                    'staff_id'         => Auth::id() ?? null,
                    'note'             => 'Cập nhật số lượng thủ công'
                ]);
            }

            return $inventory;
        });
    }

    public function deleteStock($id)
    {
        return DB::transaction(function () use ($id) {
            $inventory = $this->inventoryRepo->findById($id);

            InventoryTransactionRecorded::dispatch([
                'product_id'       => $inventory->product_id,
                'location_id'      => $inventory->location_id,
                'batch_id'         => $inventory->batch_id,
                'transaction_type' => 'adjustment',
                'reference_id'     => null,
                'quantity_change'  => -$inventory->quantity,
                'balance_after'    => 0,
                'staff_id'         => Auth::id() ?? null,
                'note'             => 'Xóa dòng tồn kho'
            ]);

            return $this->inventoryRepo->delete($id);
        });
    }

    public function deductStockForSales(int $productId, int $quantityToDeduct, ?int $referenceId = null)
    {
        return DB::transaction(function () use ($productId, $quantityToDeduct, $referenceId) {
            $inventories = $this->inventoryRepo->getPrioritizedStock($productId);
            $remaining = $quantityToDeduct;

            foreach ($inventories as $inv) {
                if ($remaining <= 0) break;

                $currentInv = $this->inventoryRepo->getLockedById($inv->id);
                if ($currentInv->quantity <= 0) continue;

                $take = min($currentInv->quantity, $remaining);

                $updateData = [
                    'quantity' => $currentInv->quantity - $take
                ];

                if ($currentInv->reserved_quantity > 0) {
                    $updateData['reserved_quantity'] = max(0, $currentInv->reserved_quantity - $take);
                }

                $this->inventoryRepo->update($currentInv->id, $updateData);

                // Ghi nhận Thẻ kho (Audit Trail)
                InventoryTransactionRecorded::dispatch([
                    'product_id'       => $currentInv->product_id,
                    'location_id'      => $currentInv->location_id,
                    'batch_id'         => $currentInv->batch_id,
                    'transaction_type' => 'outbound',
                    'reference_id'     => $referenceId,
                    'quantity_change'  => -$take,
                    'balance_after'    => $updateData['quantity'],
                    'staff_id'         => Auth::id() ?? null,
                    'note'             => 'Xuất kho bán hàng (Tự động FEFO/FIFO)'
                ]);

                $remaining -= $take;
            }

            if ($remaining > 0) {
                throw new Exception("Trục trặc hệ thống: Tồn kho thực tế không đủ để trừ.");
            }
        });
    }

    public function adjustStock(int $productId, int $locationId, ?int $batchId, int $variance, string $reason, int $staffId, int $referenceId)
    {
        return DB::transaction(function () use ($productId, $locationId, $batchId, $variance, $reason, $staffId, $referenceId) {
            if ($variance == 0) return;

            $inventory = $this->inventoryRepo->getLockedStock($productId, $locationId, $batchId);
            $balanceAfter = $variance;

            if ($inventory) {
                $balanceAfter = $inventory->quantity + $variance;

                if ($balanceAfter < 0) {
                    throw new Exception("Lỗi: Số lượng kiểm kê thực tế gây ra tồn kho âm cho SP-{$productId}.");
                }

                $this->inventoryRepo->update($inventory->id, ['quantity' => $balanceAfter]);
            } else {
                if ($variance < 0) {
                    throw new Exception("Lỗi: Không thể trừ kho SP-{$productId} ở vị trí này vì chưa từng có tồn kho.");
                }

                $this->inventoryRepo->create([
                    'product_id'        => $productId,
                    'location_id'       => $locationId,
                    'batch_id'          => $batchId,
                    'quantity'          => $variance,
                    'reserved_quantity' => 0
                ]);
            }

            InventoryTransactionRecorded::dispatch([
                'product_id'       => $productId,
                'location_id'      => $locationId,
                'batch_id'         => $batchId,
                'transaction_type' => 'adjustment',
                'reference_id'     => $referenceId,
                'quantity_change'  => $variance,
                'balance_after'    => $balanceAfter,
                'staff_id'         => $staffId,
                'note'             => $reason
            ]);
        });
    }
}
