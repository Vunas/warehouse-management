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
     * Nghiệp vụ: Cộng tồn kho an toàn (Nhập kho, Chuyển kho)
     */
    public function addStock(int $productId, int $locationId, int $quantityToAdd)
    {
        $inventory = $this->inventoryRepo->findByProductAndLocation($productId, $locationId);

        if ($inventory) {
            return $this->inventoryRepo->update($inventory->id, [
                'quantity' => $inventory->quantity + $quantityToAdd
            ]);
        } else {
            return $this->inventoryRepo->create([
                'product_id'  => $productId,
                'location_id' => $locationId,
                'quantity'    => $quantityToAdd
            ]);
        }
    }

    /**
     * Nghiệp vụ: Trừ tồn kho tại 1 location CHỈ ĐỊNH
     */
    public function deductStock(int $productId, int $locationId, int $quantityToDeduct)
    {
        $inventory = $this->inventoryRepo->findByProductAndLocation($productId, $locationId);

        if (!$inventory || $inventory->quantity < $quantityToDeduct) {
            throw new Exception("Lỗi: Location không đủ số lượng tồn kho để luân chuyển!");
        }

        return $this->inventoryRepo->update($inventory->id, [
            'quantity' => $inventory->quantity - $quantityToDeduct
        ]);
    }

    /**
     * NGHIỆP VỤ LÕI MỚI: Trừ kho tự động theo quy tắc FIFO
     */
    public function deductStockForOrder(int $productId, int $quantityToDeduct)
    {
        $inventories = $this->inventoryRepo->getAvailableStockByProduct($productId);
        
        $remainingToDeduct = $quantityToDeduct;

        foreach ($inventories as $inventory) {
            if ($remainingToDeduct <= 0) break;

            if ($inventory->quantity >= $remainingToDeduct) {
                $this->inventoryRepo->update($inventory->id, [
                    'quantity' => $inventory->quantity - $remainingToDeduct
                ]);
                $remainingToDeduct = 0;
            } else {
                $remainingToDeduct -= $inventory->quantity;
                $this->inventoryRepo->update($inventory->id, [
                    'quantity' => 0
                ]);
            }
        }

        if ($remainingToDeduct > 0) {
            throw new Exception("Sản phẩm (ID: $productId) không đủ số lượng đáp ứng yêu cầu. Thiếu: $remainingToDeduct sản phẩm.");
        }
    }
}
