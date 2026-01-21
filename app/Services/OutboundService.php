<?php

namespace App\Services;

use App\Repositories\Interfaces\ContractRepositoryInterface;
use App\Repositories\Interfaces\OutboundTicketRepositoryInterface;
use App\Repositories\Interfaces\InventoryRepositoryInterface;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class OutboundService
{
    protected $outboundRepo;
    protected $customerRepo;
    protected $inventoryRepo;
    protected $contractRepo;

    public function __construct(
        OutboundTicketRepositoryInterface $outboundRepo,
        InventoryRepositoryInterface $inventoryRepo,
        CustomerRepositoryInterface $customerRepo,
        ContractRepositoryInterface $contractRepo
    ) {
        $this->outboundRepo = $outboundRepo;
        $this->inventoryRepo = $inventoryRepo;
        $this->customerRepo = $customerRepo;
        $this->contractRepo = $contractRepo;
    }
    
    public function getOutboundHistory()
    {
        return $this->outboundRepo->paginate();
    }

    public function createTicket(array $data)
    {
        DB::beginTransaction();
        try {
            $ticket = $this->outboundRepo->create([
                'contract_id' => $data['contract_id'],
                'order_number' => $data['order_number'] ?? 'OUT-'.time(),
                'requested_date' => $data['requested_date'],
                'status' => 'PENDING'
            ]);

            foreach ($data['products'] as $item) {
                $this->outboundRepo->createDetail([
                    'outbound_id' => $ticket->id,
                    'product_id' => $item['product_id'],
                    'quantity_ordered' => $item['quantity'],
                ]);
            }

            DB::commit();
            return $ticket;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function processOutbound($ticketId)
    {
        DB::beginTransaction();
        try {
            $ticket = $this->outboundRepo->findById($ticketId);

            foreach ($ticket->details as $detail) {
                $remainingQty = $detail->quantity_ordered;

                $inventoryItems = \App\Models\InventoryItem::where('product_id', $detail->product_id)
                    ->where('quantity_on_hand', '>', 0)
                    ->orderBy('imported_at', 'asc')
                    ->get();

                foreach ($inventoryItems as $item) {
                    if ($remainingQty <= 0) break;

                    $takeQty = min($remainingQty, $item->quantity_on_hand);
                    $newQty = $item->quantity_on_hand - $takeQty;
                    $remainingQty -= $takeQty;

                    // Tính lại slot (giảm tuyến tính)
                    $newSlots = $item->slots_occupied;
                    if ($item->quantity_on_hand > 0) {
                         $ratio = $newQty / $item->quantity_on_hand;
                         $newSlots = ceil($item->slots_occupied * $ratio);
                    }

                    // UPDATE qua Repo
                    $this->inventoryRepo->updateItem($item->id, [
                        'quantity_on_hand' => $newQty,
                        'slots_occupied' => $newSlots
                    ]);

                    // LOG qua Repo
                    $this->inventoryRepo->logTransaction([
                        'item_id' => $item->id,
                        'transaction_type' => 'OUTBOUND',
                        'quantity_change' => -$takeQty,
                        'balance_before' => $item->quantity_on_hand,
                        'balance_after' => $newQty,
                        'reference_id' => $ticket->id,
                        'reference_type' => 'OUTBOUND_ORDER'
                    ]);
                }

                if ($remainingQty > 0) {
                    throw new Exception("Không đủ hàng tồn cho sản phẩm ID: " . $detail->product_id);
                }
            }

            $this->outboundRepo->updateStatus($ticketId, 'SHIPPED');
            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

        public function countPending($userid = null)
    {
        if (!$userid) {
            return $this->outboundRepo
                ->countByStatus('pending');
        }


        $customer = $this->customerRepo->findByUserId($userid);
        if (!$customer) {
            return 0;
        }
        $contracts = $this->contractRepo->getByCustomer($customer->id);
        $contractIds = $contracts->pluck('id')->toArray();
        return $this->outboundRepo
            ->countByStatus('pending', $contractIds);
    }
}