<?php

namespace App\Services;

use App\Repositories\Interfaces\InboundTicketRepositoryInterface;
use App\Repositories\Interfaces\InventoryRepositoryInterface;
use App\Repositories\Interfaces\WarehouseRepositoryInterface;
use App\Models\SizeConversionRule;
use Illuminate\Support\Facades\DB;
use Exception;

class InboundService
{
    protected $inboundRepo;
    protected $inventoryRepo;
    protected $warehouseRepo;

    public function __construct(
        InboundTicketRepositoryInterface $inboundRepo,
        InventoryRepositoryInterface $inventoryRepo,
        WarehouseRepositoryInterface $warehouseRepo
    ) {
        $this->inboundRepo = $inboundRepo;
        $this->inventoryRepo = $inventoryRepo;
        $this->warehouseRepo = $warehouseRepo;
    }

    public function getInboundHistory()
    {
        return $this->inboundRepo->paginate();
    }

    public function getTicketById($id)
    {
        return $this->inboundRepo->findById($id);
    }

    public function createTicket(array $data)
    {
        DB::beginTransaction();
        try {
            $ticket = $this->inboundRepo->create([
                'contract_id' => $data['contract_id'],
                'order_number' => $data['order_number'] ?? 'IN-' . time(),
                'expected_date' => $data['expected_date'],
                'status' => 'PENDING'
            ]);

            foreach ($data['products'] as $item) {
                // Sử dụng hàm createDetail của Repo
                $this->inboundRepo->createDetail([
                    'inbound_id' => $ticket->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'input_length' => $item['input_length'] ?? 0,
                    'input_width' => $item['input_width'] ?? 0,
                    'input_height' => $item['input_height'] ?? 0,
                ]);
            }

            DB::commit();
            return $ticket;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function approveAndCalculateSlots($ticketId)
    {
        DB::beginTransaction();
        try {
            $ticket = $this->inboundRepo->findById($ticketId);
            $rules = SizeConversionRule::where('is_active', true)->orderBy('priority', 'desc')->get();

            foreach ($ticket->details as $detail) {
                $appliedRule = null;
                $isViolation = true;

                foreach ($rules as $rule) {
                    if (
                        $detail->measured_length <= $rule->max_length &&
                        $detail->measured_width <= $rule->max_width &&
                        $detail->measured_height <= $rule->max_height
                    ) {
                        $appliedRule = $rule;
                        $isViolation = false;
                        break;
                    }
                }

                if ($isViolation && $rules->isNotEmpty()) {
                    $appliedRule = $rules->first();
                }

                // Sử dụng createCalculatedSlot của Repo
                $this->inboundRepo->createCalculatedSlot([
                    'inbound_detail_id' => $detail->detail_id ?? $detail->id,
                    'rule_id' => $appliedRule ? $appliedRule->rule_id : null,
                    'final_length' => $detail->measured_length,
                    'final_width' => $detail->measured_width,
                    'final_height' => $detail->measured_height,
                    'slots_per_unit' => $appliedRule ? $appliedRule->slot_cost : 0,
                    'total_slots_required' => ($appliedRule ? $appliedRule->slot_cost : 0) * $detail->planned_quantity,
                    'is_violation' => $isViolation
                ]);
            }

            $this->inboundRepo->updateStatus($ticketId, 'APPROVED');
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function processReception($ticketId)
    {
        DB::beginTransaction();
        try {
            $ticket = $this->inboundRepo->findById($ticketId);

            foreach ($ticket->details as $detail) {
                $calcSlot = $detail->calculatedSlot;
                $slotsNeeded = $calcSlot->total_slots_required;

                $targetBlock = $this->findAvailableBlock($ticket->contract, $slotsNeeded);

                if (!$targetBlock) {
                    throw new Exception("Không tìm thấy Block trống.");
                }

                // Dùng InventoryRepo để tạo Item
                $inventoryItem = $this->inventoryRepo->createItem([
                    'block_id' => $targetBlock->id,
                    'product_id' => $detail->product_id,
                    'inbound_detail_id' => $detail->id,
                    'slots_occupied' => $slotsNeeded,
                    'quantity_on_hand' => $detail->planned_quantity,
                    'imported_at' => now(),
                ]);

                // Dùng InventoryRepo để log
                $this->inventoryRepo->logTransaction([
                    'item_id' => $inventoryItem->id,
                    'transaction_type' => 'INBOUND',
                    'quantity_change' => $detail->planned_quantity,
                    'balance_before' => 0,
                    'balance_after' => $detail->planned_quantity,
                    'reference_id' => $ticket->id,
                    'reference_type' => 'INBOUND_ORDER'
                ]);

                // Update Block usage
                $this->warehouseRepo->updateBlock($targetBlock->id, [
                    'used_slots' => $targetBlock->used_slots + $slotsNeeded
                ]);
            }

            $this->inboundRepo->updateStatus($ticketId, 'COMPLETED');
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // Helper function giữ nguyên logic
    private function findAvailableBlock($contract, $requiredSlots)
    {
        foreach ($contract->contractBlocks as $cb) {
            $block = $cb->storageBlock;
            if (($block->total_slots - $block->used_slots) >= $requiredSlots) {
                return $block;
            }
        }
        return null;
    }
    public function countPending()
    {
        return $this->inboundRepo->countByStatus('pending');
    }

    public function getLatest($limit = 5)
    {
        return $this->inboundRepo->getLatest($limit);
    }
}
