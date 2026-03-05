<?php

namespace App\Services;

use App\Repositories\Interfaces\ContractRepositoryInterface;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
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
    protected $contractRepo;
    protected $customerRepo;

    public function __construct(
        InboundTicketRepositoryInterface $inboundRepo,
        InventoryRepositoryInterface $inventoryRepo,
        WarehouseRepositoryInterface $warehouseRepo,
        CustomerRepositoryInterface $customerRepo,
        ContractRepositoryInterface $contractRepo
    ) {
        $this->inboundRepo = $inboundRepo;
        $this->inventoryRepo = $inventoryRepo;
        $this->warehouseRepo = $warehouseRepo;
        $this->customerRepo = $customerRepo;
        $this->contractRepo = $contractRepo;
    }

    // --- Read ---
    public function getInboundHistory()
    {
        return $this->inboundRepo->paginate();
    }

    public function getTicketById($id)
    {
        return $this->inboundRepo->findById($id);
    }

    // --- Create ---
    public function createTicket(array $data)
    {
        DB::beginTransaction();
        try {
            $ticket = $this->inboundRepo->create([
                'contract_id' => $data['contract_id'],
                'expected_date' => $data['expected_date'],
                'status' => 'Pending' // Theo ERD
            ]);

            foreach ($data['products'] as $item) {
                $this->inboundRepo->createDetail([
                    'inbound_id' => $ticket->inbound_id ?? $ticket->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'input_length' => $item['input_length'],
                    'input_width' => $item['input_width'],
                    'input_height' => $item['input_height'],
                ]);
            }

            DB::commit();
            return $ticket;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // --- Update (Chỉ khi Pending) ---
    public function updateTicket($id, array $data)
    {
        $ticket = $this->inboundRepo->findById($id);
        
        // Logic: Chỉ được sửa khi chưa duyệt
        if ($ticket->status !== 'Pending') {
            throw new Exception("Không thể cập nhật phiếu đã được xử lý (Status: {$ticket->status}).");
        }

        DB::beginTransaction();
        try {
            // Update Ticket Info
            $this->inboundRepo->update($id, [
                'contract_id' => $data['contract_id'],
                'expected_date' => $data['expected_date'],
            ]);

            // Cập nhật Details: Xóa cũ -> Thêm mới (đơn giản và an toàn nhất cho quan hệ 1-n)
            // Lưu ý: Do chưa Approved nên chưa có Calculated Slots, xóa Details thoải mái.
            $ticket->details()->delete(); 

            foreach ($data['products'] as $item) {
                $this->inboundRepo->createDetail([
                    'inbound_id' => $ticket->inbound_id ?? $ticket->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'input_length' => $item['input_length'],
                    'input_width' => $item['input_width'],
                    'input_height' => $item['input_height'],
                ]);
            }

            DB::commit();
            return $ticket;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // --- Delete (Soft Delete - Chỉ khi Pending/Rejected) ---
    public function deleteTicket($id)
    {
        $ticket = $this->inboundRepo->findById($id);

        if (!in_array($ticket->status, ['Pending', 'Rejected'])) {
            throw new Exception("Không thể xóa phiếu đang xử lý hoặc đã hoàn thành.");
        }

        // Repo cần hỗ trợ delete() gọi vào SoftDeletes của Eloquent
        return $this->inboundRepo->delete($id);
    }

    // --- Reject (Nghiệp vụ từ chối phiếu) ---
    public function rejectTicket($id)
    {
        $ticket = $this->inboundRepo->findById($id);

        if ($ticket->status !== 'Pending') {
            throw new Exception("Chỉ có thể từ chối phiếu đang chờ duyệt.");
        }

        return $this->inboundRepo->updateStatus($id, 'Rejected');
    }

    // --- Approve Logic (Giữ nguyên, cập nhật status theo ERD) ---
    public function approveAndCalculateSlots($ticketId)
    {
        DB::beginTransaction();
        try {
            $ticket = $this->inboundRepo->findById($ticketId);
            $rules = SizeConversionRule::where('is_active', true)->orderBy('priority_level', 'desc')->get(); // ERD: priority_level

            foreach ($ticket->details as $detail) {
                // Logic tính toán slot (giả lập)...
                $appliedRule = $rules->first(); // Simplification for demo
                
                // ERD: CALCULATED_SLOTS
                $this->inboundRepo->createCalculatedSlot([
                    'inbound_detail_id' => $detail->detail_id ?? $detail->id, // ERD: inbound_detail_id
                    'rule_id' => $appliedRule ? $appliedRule->rule_id : null,
                    'final_length' => $detail->input_length,
                    'final_width' => $detail->input_width,
                    'final_height' => $detail->input_height,
                    'final_slot_cost' => ($appliedRule ? $appliedRule->slot_cost : 1) * $detail->quantity,
                    'is_violation' => false
                ]);
            }

            $this->inboundRepo->updateStatus($ticketId, 'Approved'); // Theo ERD
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // --- Process Reception (Giữ nguyên) ---
    public function processReception($ticketId)
    {
        // Logic nhập kho vật lý...
        // Status cuối cùng có thể là 'Completed' hoặc 'Received' tùy convention team, 
        // nhưng ERD không ghi status thứ 4, giả sử Approved là xong bước giấy tờ, 
        // bước này là bước nhập kho thực tế (Inventory Transaction).
        
        // ... (Logic tạo Inventory Items & Transactions như cũ) ...
        
        // Nếu cần status khác ngoài ERD để đánh dấu hoàn tất nhập kho:
        // $this->inboundRepo->updateStatus($ticketId, 'Received'); 
    }
    
    // ... (Các hàm helper khác giữ nguyên) ...
    public function countPending($userid = null) { /* ... */ }
    public function getLatest($limit = 5, $userid = null) { /* ... */ }
}