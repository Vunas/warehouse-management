<?php

namespace App\Services;

use App\Repositories\Interfaces\InboundOrderRepositoryInterface;
use App\Repositories\Interfaces\InboundItemRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class InboundOrderService
{
    protected $inboundOrderRepo;
    protected $inboundItemRepo;
    protected $inventoryService;

    public function __construct(
        InboundOrderRepositoryInterface $inboundOrderRepo,
        InboundItemRepositoryInterface $inboundItemRepo,
        InventoryService $inventoryService 
    ) {
        $this->inboundOrderRepo = $inboundOrderRepo;
        $this->inboundItemRepo = $inboundItemRepo;
        $this->inventoryService = $inventoryService;
    }

    public function getPaginatedInbounds($perPage = 15)
    {
        return $this->inboundOrderRepo->paginate($perPage, ['*'], ['supplier', 'staff']);
    }

    public function createInboundOrder(array $data)
    {
        $data['status'] = 'pending';
        return $this->inboundOrderRepo->create($data);
    }

    /**
     * NGHIỆP VỤ LÕI: Hoàn tất phiếu nhập kho và CỘNG tồn kho
     */
    public function completeInboundOrder($inboundId, array $shelfAssignments)
    {
        $order = $this->inboundOrderRepo->findById($inboundId);

        if ($order->status !== 'pending') {
            throw new Exception("Chỉ có thể hoàn tất phiếu đang ở trạng thái pending.");
        }

        return DB::transaction(function () use ($inboundId, $shelfAssignments) {
            // 1. Cập nhật trạng thái phiếu nhập
            $order = $this->inboundOrderRepo->update($inboundId, ['status' => 'completed']);

            // 2. Lấy chi tiết các món hàng vừa nhập
            $items = $this->inboundItemRepo->getByInboundId($inboundId);

            foreach ($items as $item) {
                if (!isset($shelfAssignments[$item->id])) {
                    throw new Exception("Sản phẩm {$item->product_id} chưa được chỉ định kệ lưu trữ.");
                }

                $shelfId = $shelfAssignments[$item->id];

                // 3. Gọi InventoryService để cộng tồn kho
                $this->inventoryService->addStock($item->product_id, $shelfId, $item->quantity);
            }

            return $order;
        });
    }

    public function cancelInboundOrder($inboundId)
    {
        return $this->inboundOrderRepo->update($inboundId, ['status' => 'cancelled']);
    }
}