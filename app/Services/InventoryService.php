<?php

namespace App\Services;

use App\Models\InternalTransfer;
use App\Models\TransferItem;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\StorageBlock;
use Illuminate\Support\Facades\DB;
use Exception;

class InventoryService
{
    public function createTransfer(array $data)
    {
        DB::beginTransaction();
        try {
            $transfer = InternalTransfer::create([
                'from_block_id' => $data['from_block_id'],
                'to_block_id' => $data['to_block_id'],
                'trigger_reason' => $data['trigger_reason'],
                'status' => 'pending'
            ]);

            foreach ($data['items'] as $item) {
                TransferItem::create([
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
            $transfer = InternalTransfer::with('items.inventoryItem')->findOrFail($transferId);
            $targetBlock = StorageBlock::findOrFail($transfer->to_block_id);

            foreach ($transfer->items as $tItem) {
                $sourceItem = $tItem->inventoryItem;
                $moveQty = $tItem->quantity;

                // 1. Trừ kho nguồn
                if ($sourceItem->current_quantity < $moveQty) {
                    throw new Exception("Không đủ hàng để chuyển tại nguồn.");
                }

                $sourceItem->current_quantity -= $moveQty;
                // Cập nhật slot used nguồn (tạm tính logic đơn giản)
                $sourceItem->slot_used = ceil($sourceItem->slot_used * ($sourceItem->current_quantity / ($sourceItem->current_quantity + $moveQty)));
                
                if ($sourceItem->current_quantity == 0) {
                    $sourceItem->delete();
                } else {
                    $sourceItem->save();
                }

                // Log Source Transaction
                InventoryTransaction::create([
                    'item_id' => $sourceItem->id,
                    'transaction_type' => 'transfer',
                    'quantity' => -$moveQty,
                    'reference_id' => $transfer->id,
                    'reference_type' => InternalTransfer::class
                ]);

                // 2. Cộng kho đích (Tạo item mới hoặc merge vào item cũ)
                // Ở đây tạo Item mới cho đơn giản và đúng logic FIFO (giữ nguyên imported_at)
                $newItem = InventoryItem::create([
                    'block_id' => $targetBlock->id,
                    'product_id' => $sourceItem->product_id,
                    'calc_id' => $sourceItem->calc_id, // Giữ nguyên thông số kích thước
                    'slot_used' => 0, // Cần tính toán lại dựa trên Size Rule, tạm để 0
                    'imported_at' => $sourceItem->imported_at, // Quan trọng: Giữ nguyên ngày nhập gốc
                    'current_quantity' => $moveQty
                ]);
                
                // Recalculate slot for new item 
                // $newItem->slot_used = ... 
                $newItem->save();

                // Log Target Transaction
                InventoryTransaction::create([
                    'item_id' => $newItem->id,
                    'transaction_type' => 'transfer',
                    'quantity' => $moveQty,
                    'reference_id' => $transfer->id,
                    'reference_type' => InternalTransfer::class
                ]);
            }

            $transfer->update(['status' => 'completed']);
            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}