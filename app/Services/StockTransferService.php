<?php

namespace App\Services;

use App\Repositories\Interfaces\StockTransferRepositoryInterface;
use App\Repositories\Interfaces\TransferItemRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class StockTransferService
{
    protected $transferRepo;
    protected $transferItemRepo;
    protected $inventoryService;

    public function __construct(
        StockTransferRepositoryInterface $transferRepo,
        TransferItemRepositoryInterface $transferItemRepo,
        InventoryService $inventoryService
    ) {
        $this->transferRepo = $transferRepo;
        $this->transferItemRepo = $transferItemRepo;
        $this->inventoryService = $inventoryService;
    }

    public function createTransfer(array $data)
    {
        $data['status'] = 'pending';
        return $this->transferRepo->create($data);
    }

    /**
     * NGHIỆP VỤ LÕI: Hoàn tất luân chuyển kho
     */
    public function completeTransfer($transferId)
    {
        $transfer = $this->transferRepo->findById($transferId);

        if ($transfer->status !== 'pending') {
            throw new Exception("Phiếu chuyển kho này đã được xử lý.");
        }

        return DB::transaction(function () use ($transfer) {
            $this->transferRepo->update($transfer->id, ['status' => 'completed']);

            $items = $this->transferItemRepo->getByTransferId($transfer->id);

            foreach ($items as $item) {
                // 1. Trừ tồn kho ở kệ cũ
                $this->inventoryService->deductStock($item->inventory->product_id, $transfer->from_shelf_id, $item->quantity);
                
                // 2. Cộng tồn kho ở kệ mới
                $this->inventoryService->addStock($item->inventory->product_id, $transfer->to_shelf_id, $item->quantity);
            }

            return $transfer;
        });
    }
}