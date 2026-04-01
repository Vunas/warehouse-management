<?php

namespace App\Services;

use App\Models\Inventory;
use App\Repositories\Interfaces\InventoryRepositoryInterface;
use Exception;

class InventoryService
{
    protected $inventoryRepo;

    public function __construct(InventoryRepositoryInterface $inventoryRepo)
    {
        $this->inventoryRepo = $inventoryRepo;
    }

    public function getInventoryByProduct($productId)
    {
        return $this->inventoryRepo->getByProductId($productId);
    }

    /**
     * CỘNG TỒN KHO (Tích hợp Lô Hàng)
     */
    public function addStock(int $productId, int $locationId, ?int $batchId, int $quantityToAdd)
    {
        // Sử dụng Query Builder trực tiếp để tránh lỗi hàm Repo tự viết
        $query = Inventory::where('product_id', $productId)->where('location_id', $locationId);

        if ($batchId) {
            $query->where('batch_id', $batchId);
        } else {
            $query->whereNull('batch_id');
        }

        $inventory = $query->first();

        if ($inventory) {
            return $inventory->update([
                'quantity' => $inventory->quantity + $quantityToAdd
            ]);
        } else {
            return Inventory::create([
                'product_id'        => $productId,
                'location_id'       => $locationId,
                'batch_id'          => $batchId, // Có thể null
                'quantity'          => $quantityToAdd,
                'reserved_quantity' => 0
            ]);
        }
    }


    /**
     * GIỮ CHỖ TỒN KHO (Gọi khi KHÁCH VỪA ĐẶT HÀNG THÀNH CÔNG)
     */
    public function reserveStock(int $productId, int $quantityToReserve)
    {
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
    }

    /**
     * HỦY GIỮ CHỖ TỒN KHO (Gọi khi KHÁCH HỦY ĐƠN HÀNG)
     */
    public function releaseReservedStock(int $productId, int $quantityToRelease)
    {
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
    }

    /**
     * TRỪ KHO TỰ ĐỘNG THEO FIFO (Gọi khi HOÀN TẤT PHIẾU XUẤT KHO)
     * $isSales = true : Trừ cho đơn hàng bán (Trừ cả quantity và reserved_quantity)
     * $isSales = false: Trừ nội bộ/điều chỉnh (Chỉ trừ quantity vào phần available)
     */
    public function deductStockFifo(int $productId, int $quantityToDeduct, bool $isSales = true)
    {
        // Chọn tập dữ liệu tồn kho dựa trên loại xuất
        $inventories = $isSales
            ? $this->inventoryRepo->getReservedStockByProduct($productId)
            : $this->inventoryRepo->getAvailableStockByProduct($productId);

        $remaining = $quantityToDeduct;

        foreach ($inventories as $inv) {
            if ($remaining <= 0) break;

            // Tính số lượng có thể lấy từ dòng (bin/kệ) này
            $takeable = $isSales
                ? $inv->reserved_quantity
                : ($inv->quantity - $inv->reserved_quantity);

            if ($takeable <= 0) continue;

            $take = min($takeable, $remaining);

            // Cập nhật số liệu
            $updateData = ['quantity' => $inv->quantity - $take];
            if ($isSales) {
                // Nếu là đơn hàng bán, phải trừ đi số lượng đã giữ chỗ
                $updateData['reserved_quantity'] = $inv->reserved_quantity - $take;
            }

            $this->inventoryRepo->update($inv->id, $updateData);

            $remaining -= $take;
        }

        if ($remaining > 0) {
            throw new Exception("Sản phẩm (ID: $productId) không đủ lượng tồn kho trên kệ để xuất. Cần kiểm tra lại thực tế!");
        }
    }
}
