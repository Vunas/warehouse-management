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

    public function getPaginatedInventories(int $perPage)
    {
        return $this->inventoryRepo->getPaginated($perPage);
    }

    public function getInventoryById($id)
    {
        return $this->inventoryRepo->findById($id);
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

            // Bắn sự kiện ghi log Transaction
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

            // Chỉ ghi log nếu thực sự có thay đổi số lượng
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

    public function reserveStock(int $productId, int $quantityToReserve)
    {
        return DB::transaction(function () use ($productId, $quantityToReserve) {
            $inventories = $this->inventoryRepo->getAvailableStockByProduct($productId);
            $remaining = $quantityToReserve;

            foreach ($inventories as $inv) {
                if ($remaining <= 0) break;

                $available = $inv->quantity - $inv->reserved_quantity;
                $reserveAmount = min($available, $remaining);

                $this->inventoryRepo->update($inv->id, [
                    'reserved_quantity' => $inv->reserved_quantity + $reserveAmount
                ]);

                $remaining -= $reserveAmount;
            }

            if ($remaining > 0) {
                throw new Exception("Sản phẩm (ID: $productId) không đủ tồn kho khả dụng để giữ chỗ. Thiếu: $remaining.");
            }
        });
    }

    public function releaseReservedStock(int $productId, int $quantityToRelease)
    {
        return DB::transaction(function () use ($productId, $quantityToRelease) {
            $inventories = $this->inventoryRepo->getReservedStockByProduct($productId);
            $remaining = $quantityToRelease;

            foreach ($inventories as $inv) {
                if ($remaining <= 0) break;

                $releaseAmount = min($inv->reserved_quantity, $remaining);

                $this->inventoryRepo->update($inv->id, [
                    'reserved_quantity' => $inv->reserved_quantity - $releaseAmount
                ]);

                $remaining -= $releaseAmount;
            }
        });
    }

        public function deductExactStock(int $productId, int $locationId, ?int $batchId, int $quantityToDeduct, bool $isSales = true, ?int $referenceId = null, string $note = '')
    {
        // Khóa dòng (Lock For Update) để tránh race condition
        $inventory = $this->inventoryRepo->getLockedStock($productId, $locationId, $batchId);

        if (!$inventory || $inventory->quantity < $quantityToDeduct) {
            throw new Exception("Lỗi: Sản phẩm ID {$productId} tại vị trí ID {$locationId} không đủ số lượng để trừ!");
        }

        $updateData = ['quantity' => $inventory->quantity - $quantityToDeduct];
        if ($isSales) {
            $updateData['reserved_quantity'] = $inventory->reserved_quantity - $quantityToDeduct;
        }

        $this->inventoryRepo->update($inventory->id, $updateData);

        // Bắn sự kiện ghi log
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

    public function deductStockFifo(int $productId, int $quantityToDeduct, bool $isSales = true, ?int $referenceId = null)
    {
        return DB::transaction(function () use ($productId, $quantityToDeduct, $isSales, $referenceId) {
            $inventories = $isSales
                ? $this->inventoryRepo->getReservedStockByProduct($productId)
                : $this->inventoryRepo->getAvailableStockByProduct($productId);

            $remaining = $quantityToDeduct;

            foreach ($inventories as $inv) {
                if ($remaining <= 0) break;

                $takeable = $isSales
                    ? $inv->reserved_quantity
                    : ($inv->quantity - $inv->reserved_quantity);

                if ($takeable <= 0) continue;

                $take = min($takeable, $remaining);

                $updateData = ['quantity' => $inv->quantity - $take];
                if ($isSales) {
                    $updateData['reserved_quantity'] = $inv->reserved_quantity - $take;
                }

                $this->inventoryRepo->update($inv->id, $updateData);

                InventoryTransactionRecorded::dispatch([
                    'product_id'       => $inv->product_id,
                    'location_id'      => $inv->location_id,
                    'batch_id'         => $inv->batch_id,
                    'transaction_type' => 'outbound',
                    'reference_id'     => $referenceId,
                    'quantity_change'  => -$take,
                    'balance_after'    => $inv->quantity - $take,
                    'staff_id'         => Auth::id() ?? null,
                    'note'             => $isSales ? 'Xuất kho bán hàng' : 'Xuất kho nội bộ/điều chỉnh'
                ]);

                $remaining -= $take;
            }

            if ($remaining > 0) {
                throw new Exception("Sản phẩm (ID: $productId) không đủ lượng tồn kho trên kệ để xuất.");
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