<?php

namespace App\Services;

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
     * Nghiệp vụ: Cộng tồn kho an toàn
     */
    public function addStock(int $productId, int $shelfId, int $quantityToAdd)
    {
        // Kiểm tra xem kệ này đã có sản phẩm này chưa
        $inventories = $this->inventoryRepo->getByShelfId($shelfId);
        $inventory = $inventories->where('product_id', $productId)->first();

        if ($inventory) {
            // Đã có -> Cập nhật số lượng
            return $this->inventoryRepo->update($inventory->id, [
                'quantity' => $inventory->quantity + $quantityToAdd
            ]);
        } else {
            // Chưa có -> Tạo mới dòng tồn kho
            return $this->inventoryRepo->create([
                'product_id' => $productId,
                'shelf_id'   => $shelfId,
                'quantity'   => $quantityToAdd
            ]);
        }
    }

    /**
     * Nghiệp vụ: Trừ tồn kho an toàn
     */
    public function deductStock(int $productId, int $shelfId, int $quantityToDeduct)
    {
        $inventories = $this->inventoryRepo->getByShelfId($shelfId);
        $inventory = $inventories->where('product_id', $productId)->first();

        if (!$inventory || $inventory->quantity < $quantityToDeduct) {
            throw new Exception("Lỗi: Kệ hàng không đủ số lượng tồn kho để xuất/chuyển!");
        }

        return $this->inventoryRepo->update($inventory->id, [
            'quantity' => $inventory->quantity - $quantityToDeduct
        ]);
    }
}