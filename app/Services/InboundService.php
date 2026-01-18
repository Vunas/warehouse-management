<?php

namespace App\Services;

use App\Models\InboundTicket;
use App\Models\InboundDetail;
use App\Models\CalculatedSlot;
use App\Models\SizeConversionRule;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\StorageBlock;
use Illuminate\Support\Facades\DB;
use Exception;

class InboundService
{
    // 1. Tạo phiếu nhập (Draft)
    public function createTicket(array $data)
    {
        DB::beginTransaction();
        try {
            $ticket = InboundTicket::create([
                'contract_id' => $data['contract_id'],
                'expected_date' => $data['expected_date'],
                'status' => 'pending'
            ]);

            foreach ($data['products'] as $item) {
                InboundDetail::create([
                    'inbound_id' => $ticket->id,
                    'product_id' => $item['product_id'],
                    'input_length' => $item['input_length'],
                    'input_width' => $item['input_width'],
                    'input_height' => $item['input_height'],
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

    // 2. Duyệt phiếu & Tính toán Slot (Core Logic)
    public function approveAndCalculateSlots($ticketId)
    {
        DB::beginTransaction();
        try {
            $ticket = InboundTicket::with('details')->findOrFail($ticketId);
            $rules = SizeConversionRule::where('is_active', true)
                        ->orderBy('priority_level', 'desc') // Check từ lớn xuống nhỏ
                        ->get();

            foreach ($ticket->details as $detail) {
                // Logic tìm Rule phù hợp
                $appliedRule = null;
                $isViolation = true; // Mặc định là vi phạm nếu không khớp rule nào

                foreach ($rules as $rule) {
                    // Kiểm tra kích thước có nằm trong giới hạn của Rule không
                    if ($detail->input_length <= $rule->max_length &&
                        $detail->input_width <= $rule->max_width &&
                        $detail->input_height <= $rule->max_height) {
                        
                        $appliedRule = $rule;
                        $isViolation = false;
                        
                        // Rule được sắp xếp ưu tiên, khớp cái nào lấy cái đó (Smallest Fit)
                        // Tuy nhiên logic ở đây đang là check từ Lớn -> Nhỏ để tìm Max Limit
                        // Nếu muốn tối ưu  sort ASC và lấy cái đầu tiên vừa vặn.
                    }
                }
                
                // Fallback: Nếu vẫn chưa tìm được (nhỏ hơn cả min), lấy rule nhỏ nhất
                if ($isViolation && $rules->isNotEmpty()) {
                     // Logic xử lý vi phạm hoặc gán mặc định rule lớn nhất
                     $appliedRule = $rules->first(); // Lấy rule lớn nhất để tính phí phạt
                }

                CalculatedSlot::create([
                    'inbound_detail_id' => $detail->id,
                    'rule_id' => $appliedRule ? $appliedRule->id : null,
                    'final_length' => $detail->input_length, // Có thể làm tròn
                    'final_width' => $detail->input_width,
                    'final_height' => $detail->input_height,
                    'final_slot_cost' => $appliedRule ? $appliedRule->slot_cost : 0,
                    'is_violation' => $isViolation
                ]);
            }

            $ticket->update(['status' => 'approved']);
            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // 3. Thực hiện nhập kho (Tạo Inventory Item)
    public function processReception($ticketId)
    {
        DB::beginTransaction();
        try {
            $ticket = InboundTicket::with(['details.calculatedSlot', 'contract.contractBlocks.storageBlock'])->findOrFail($ticketId);

            foreach ($ticket->details as $detail) {
                // Logic đơn giản: Tìm Block đầu tiên còn chỗ trong hợp đồng để nhét vào
                // Thực tế cần thuật toán Bin Packing phức tạp hơn
                $targetBlock = $this->findAvailableBlock($ticket->contract, $detail->calculatedSlot->final_slot_cost * $detail->quantity);

                if (!$targetBlock) {
                    throw new Exception("Không tìm thấy Lô/Kệ trống phù hợp cho sản phẩm " . $detail->product_id);
                }

                // Tạo Inventory Item
                $inventoryItem = InventoryItem::create([
                    'block_id' => $targetBlock->id,
                    'product_id' => $detail->product_id,
                    'calc_id' => $detail->calculatedSlot->id,
                    'slot_used' => $detail->calculatedSlot->final_slot_cost * $detail->quantity,
                    'imported_at' => now(),
                    'current_quantity' => $detail->quantity,
                ]);

                // Log Transaction
                InventoryTransaction::create([
                    'item_id' => $inventoryItem->id,
                    'transaction_type' => 'inbound',
                    'quantity' => $detail->quantity,
                    'reference_id' => $ticket->id,
                    'reference_type' => InboundTicket::class
                ]);
            }

            $ticket->update(['status' => 'received']);
            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function findAvailableBlock($contract, $requiredSlots)
    {
        // Lấy danh sách các Block mà khách hàng này đã thuê
        foreach ($contract->contractBlocks as $cb) {
            $block = $cb->storageBlock;
            if ($block->available_slots >= $requiredSlots) {
                return $block;
            }
        }
        return null;
    }
}