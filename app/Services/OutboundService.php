<?php

namespace App\Services;

use App\Models\OutboundTicket;
use App\Models\OutboundDetail;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;
use Exception;

class OutboundService
{
    public function createTicket(array $data)
    {
        DB::beginTransaction();
        try {
            $ticket = OutboundTicket::create([
                'contract_id' => $data['contract_id'],
                'requested_date' => $data['requested_date'],
                'status' => 'pending'
            ]);

            foreach ($data['products'] as $item) {
                OutboundDetail::create([
                    'outbound_id' => $ticket->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            DB::commit();
            return $ticket;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // Xử lý xuất kho (FIFO Logic)
    public function processOutbound($ticketId)
    {
        DB::beginTransaction();
        try {
            $ticket = OutboundTicket::with('details')->findOrFail($ticketId);

            foreach ($ticket->details as $detail) {
                $remainingQty = $detail->quantity;

                // FIFO: Lấy các lô hàng cũ nhất của sản phẩm này
                // Chỉ lấy hàng thuộc về contract của khách (nếu inventory có link contract, ở đây giả sử inventory link qua block->contract hoặc logic check owner)
                // Đơn giản hóa: Lấy inventory item có product_id tương ứng trong các kho.
                
                $inventoryItems = InventoryItem::where('product_id', $detail->product_id)
                    ->where('current_quantity', '>', 0)
                    ->orderBy('imported_at', 'asc') // Cũ nhất trước
                    ->get();

                foreach ($inventoryItems as $item) {
                    if ($remainingQty <= 0) break;

                    $takeQty = 0;
                    if ($item->current_quantity >= $remainingQty) {
                        // Lô này đủ hàng
                        $takeQty = $remainingQty;
                        $item->current_quantity -= $takeQty;
                        $remainingQty = 0;
                    } else {
                        // Lô này không đủ, lấy hết và tìm lô tiếp theo
                        $takeQty = $item->current_quantity;
                        $remainingQty -= $takeQty;
                        $item->current_quantity = 0;
                    }

                    // Cập nhật lại số slot chiếm dụng (giả sử tuyến tính)
                    // Hoặc giữ nguyên slot_used nếu item vẫn còn đó (tùy logic)
                    // Ở đây update slot_used giảm theo tỷ lệ
                    if ($item->current_quantity == 0) {
                         $item->slot_used = 0; // Giải phóng slot
                         $item->delete(); // Soft delete nếu hết hàng
                    } else {
                         // Tính lại slot used nếu cần
                    }
                    
                    $item->save();

                    // Log Transaction
                    InventoryTransaction::create([
                        'item_id' => $item->id, // Lưu ý: nếu soft delete thì vẫn link được ID
                        'transaction_type' => 'outbound',
                        'quantity' => -$takeQty, // Số âm
                        'reference_id' => $ticket->id,
                        'reference_type' => OutboundTicket::class
                    ]);
                }

                if ($remainingQty > 0) {
                    throw new Exception("Không đủ hàng tồn kho cho sản phẩm ID: " . $detail->product_id);
                }
            }

            $ticket->update(['status' => 'completed']);
            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}