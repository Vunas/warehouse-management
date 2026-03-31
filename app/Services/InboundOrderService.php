<?php

namespace App\Services;

use App\Repositories\Interfaces\InboundOrderRepositoryInterface;
use App\Repositories\Interfaces\InboundItemRepositoryInterface;
use App\Models\ProductBatch;
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

    public function completeInboundOrder($inboundId, array $assignments)
    {
        $order = $this->inboundOrderRepo->findById($inboundId);

        if ($order->status !== 'pending') {
            throw new Exception("Chỉ có thể hoàn tất phiếu đang ở trạng thái pending.");
        }

        return DB::transaction(function () use ($inboundId, $assignments) {
            // Đánh dấu hoàn tất phiếu nhập
            $order = $this->inboundOrderRepo->update($inboundId, ['status' => 'completed']);
            $items = $this->inboundItemRepo->getByInboundId($inboundId);

            foreach ($items as $item) {
                // Kiểm tra xem item có trong mảng assignment không
                if (!isset($assignments[$item->id])) {
                    throw new Exception("Sản phẩm {$item->product->name} chưa được chỉ định vị trí lưu kho.");
                }

                $assign = $assignments[$item->id];
                $locationId = $assign['location_id'];
                $batchCode = $assign['batch_code'] ?? null;
                $expiryDate = $assign['expiry_date'] ?? null;
                $manufactureDate = $assign['manufacture_date'] ?? null;
                
                $batchId = null;

                // XỬ LÝ LÔ HÀNG (BATCH/LOT)
                if (!empty($batchCode)) {
                    $batch = ProductBatch::firstOrCreate(
                        [
                            'product_id' => $item->product_id, 
                            'batch_code' => $batchCode
                        ],
                        [
                            'expiry_date' => !empty($expiryDate) ? $expiryDate : null, 
                            'manufacture_date' => !empty($manufactureDate) ? $manufactureDate : null
                        ]
                    );
                    
                    $batchId = $batch->id;

                    // QUAN TRỌNG: Lưu batch_id lại vào chi tiết phiếu nhập (inbound_items)
                    // Để sau này xem lại lịch sử biết item này thuộc lô nào
                    $this->inboundItemRepo->update($item->id, ['batch_id' => $batchId]);
                }

                // CỘNG TỒN KHO VÀO VỊ TRÍ ĐÃ CHỌN
                $this->inventoryService->addStock($item->product_id, $locationId, $batchId, $item->quantity);
            }

            return $order;
        });
    }

    public function cancelInboundOrder($inboundId)
    {
        return $this->inboundOrderRepo->update($inboundId, ['status' => 'cancelled']);
    }
}