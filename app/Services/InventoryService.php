<?php

namespace App\Services;

use App\Repositories\Interfaces\InventoryRepositoryInterface;
use App\Repositories\Interfaces\WarehouseRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class InventoryService
{
    protected $inventoryRepo;
    protected $warehouseRepo;

    public function __construct(
        InventoryRepositoryInterface $inventoryRepo,
        WarehouseRepositoryInterface $warehouseRepo
    ) {
        $this->inventoryRepo = $inventoryRepo;
        $this->warehouseRepo = $warehouseRepo;
    }

    public function getTransfersPaginated($perPage= 10){
        return $this->inventoryRepo->getTransfersPaginated($perPage);
    }

    public function createTransfer(array $data)
    {
        DB::beginTransaction();
        try {
            $transfer = $this->inventoryRepo->createTransfer([
                'from_block_id' => $data['from_block_id'],
                'to_block_id' => $data['to_block_id'],
                'trigger_reason' => $data['trigger_reason'],
                'status' => 'PENDING'
            ]);

            foreach ($data['items'] as $item) {
                $this->inventoryRepo->createTransferItem([
                    'transfer_id' => $transfer->id,
                    'item_id' => $item['inventory_item_id'],
                    'quantity' => $item['quantity']
                ]);
            }

            DB::commit();
            return $transfer;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function executeTransfer($transferId)
    {
        DB::beginTransaction();
        try {
            // Lấy transfer kèm items
            $transfer = $this->inventoryRepo->findTransferById($transferId);
            $targetBlock = $this->warehouseRepo->findBlockById($transfer->to_block_id);

            foreach ($transfer->items as $tItem) {
                $sourceItem = $tItem->inventoryItem;
                $moveQty = $tItem->quantity;

                // 1. Trừ kho nguồn
                if ($sourceItem->quantity_on_hand < $moveQty) {
                    throw new Exception("Không đủ hàng để chuyển tại nguồn.");
                }

                $newSourceQty = $sourceItem->quantity_on_hand - $moveQty;
                // Tính lại slot nguồn
                $newSourceSlot = ceil($sourceItem->slots_occupied * ($newSourceQty / $sourceItem->quantity_on_hand));

                $this->inventoryRepo->updateItem($sourceItem->id ?? $sourceItem->item_id, [
                    'quantity_on_hand' => $newSourceQty,
                    'slots_occupied' => $newSourceSlot
                ]);

                $this->inventoryRepo->logTransaction([
                    'item_id' => $sourceItem->id ?? $sourceItem->item_id,
                    'transaction_type' => 'TRANSFER_OUT',
                    'quantity_change' => -$moveQty,
                    'balance_before' => $sourceItem->quantity_on_hand,
                    'balance_after' => $newSourceQty,
                    'reference_id' => $transfer->id ?? $transfer->transfer_id,
                    'reference_type' => 'INTERNAL_TRANSFER'
                ]);

                // 2. Cộng kho đích (Tạo item mới để giữ trace FIFO)
                $newItem = $this->inventoryRepo->createItem([
                    'block_id' => $targetBlock->id ?? $targetBlock->block_id,
                    'product_id' => $sourceItem->product_id,
                    'inbound_detail_id' => $sourceItem->inbound_detail_id, // Giữ nguyên trace
                    'slots_occupied' => 0, // Cần tính lại slot dựa trên rule nếu cần
                    'imported_at' => $sourceItem->imported_at, // Giữ nguyên ngày nhập
                    'quantity_on_hand' => $moveQty
                ]);

                $this->inventoryRepo->logTransaction([
                    'item_id' => $newItem->id ?? $newItem->item_id,
                    'transaction_type' => 'TRANSFER_IN',
                    'quantity_change' => $moveQty,
                    'balance_before' => 0,
                    'balance_after' => $moveQty,
                    'reference_id' => $transfer->id ?? $transfer->transfer_id,
                    'reference_type' => 'INTERNAL_TRANSFER'
                ]);
            }

            $this->inventoryRepo->updateTransferStatus($transferId, 'COMPLETED');
            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
        public function searchInventory(array $filters)
    {
        return $this->inventoryRepo->searchInventory($filters);
    }

    public function getTotalUsedSlots()
    {
        return $this->inventoryRepo->sumTotalUsedSlots();
    }
}