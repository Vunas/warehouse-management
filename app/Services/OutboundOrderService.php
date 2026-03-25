<?php

namespace App\Services;

use App\Models\OutboundOrder;
use Illuminate\Support\Facades\DB;
use Exception;

class OutboundOrderService
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function getPaginatedOutbounds($perPage = 15)
    {
        return OutboundOrder::with(['order', 'staff'])->orderBy('id', 'desc')->paginate($perPage);
    }

    public function createOutboundOrder(array $data)
    {
        $data['status'] = 'pending';
        return OutboundOrder::create($data);
    }

    public function completeOutboundOrder($outboundId)
    {
        $outbound = OutboundOrder::with('items')->findOrFail($outboundId);

        if ($outbound->status !== 'pending') {
            throw new Exception("Phiếu xuất kho đã được xử lý trước đó.");
        }

        return DB::transaction(function () use ($outbound) {
            $outbound->update(['status' => 'completed']);

            foreach ($outbound->items as $item) {
                // Tự động trừ tồn kho theo FIFO
                $this->inventoryService->deductStockForOrder($item->product_id, $item->quantity);
            }

            return $outbound;
        });
    }

    public function cancelOutboundOrder($outboundId)
    {
        return OutboundOrder::where('id', $outboundId)->update(['status' => 'cancelled']);
    }
}