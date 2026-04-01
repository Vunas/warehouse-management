<?php

namespace App\Services;

use App\Repositories\Interfaces\InboundOrderRepositoryInterface;
use App\Repositories\Interfaces\InboundItemRepositoryInterface;
use App\Models\Supplier;
use App\Models\Location;
use App\Models\Product;
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

    public function getCreateData()
    {
        return ['suppliers' => Supplier::all()];
    }

    public function getShowData($id)
    {
        $inbound = $this->inboundOrderRepo->findById($id, ['*'], ['items.product', 'items.batch', 'supplier', 'staff']);
        $locations = Location::where('is_store', true)->get();
        $products = Product::where('is_active', 1)->get();

        return compact('inbound', 'locations', 'products');
    }

    public function createInboundOrder(array $data)
    {
        $data['status'] = 'pending';
        return $this->inboundOrderRepo->create($data);
    }

    public function addItem($inboundId, array $data)
    {
        return DB::transaction(function () use ($inboundId, $data) {
            $inbound = $this->inboundOrderRepo->findById($inboundId);
            if ($inbound->status !== 'pending') {
                throw new Exception("Không thể thêm SP vào phiếu đã duyệt.");
            }

            $existingItem = $this->inboundItemRepo->findByInboundAndProduct($inboundId, $data['product_id']);

            if ($existingItem) {
                return $this->inboundItemRepo->update($existingItem->id, [
                    'quantity' => $existingItem->quantity + $data['quantity'],
                    'price'    => $data['price']
                ]);
            }

            $data['inbound_id'] = $inboundId;
            return $this->inboundItemRepo->create($data);
        });
    }

    public function updateItem($inboundId, $itemId, array $data)
    {
        return DB::transaction(function () use ($inboundId, $itemId, $data) {
            $inbound = $this->inboundOrderRepo->findById($inboundId);
            if ($inbound->status !== 'pending') {
                throw new Exception("Phiếu đã chốt, không thể chỉnh sửa.");
            }

            return $this->inboundItemRepo->update($itemId, [
                'quantity' => $data['quantity'],
                'price'    => $data['price'],
            ]);
        });
    }

    public function removeItem($inboundId, $itemId)
    {
        return DB::transaction(function () use ($inboundId, $itemId) {
            $inbound = $this->inboundOrderRepo->findById($inboundId);
            if ($inbound->status !== 'pending') {
                throw new Exception("Phiếu đã khóa, không thể xóa.");
            }

            return $this->inboundItemRepo->delete($itemId);
        });
    }

    public function completeInboundOrder($inboundId, array $assignments)
    {
        $order = $this->inboundOrderRepo->findById($inboundId);

        if ($order->status !== 'pending') {
            throw new Exception("Chỉ có thể hoàn tất phiếu đang ở trạng thái pending.");
        }

        return DB::transaction(function () use ($inboundId, $assignments) {
            $order = $this->inboundOrderRepo->update($inboundId, ['status' => 'completed']);
            $items = $this->inboundItemRepo->getByInboundId($inboundId);

            foreach ($items as $item) {
                if (!isset($assignments[$item->id])) {
                    throw new Exception("Sản phẩm {$item->product->name} chưa được chỉ định vị trí lưu kho.");
                }

                $assign = $assignments[$item->id];
                $locationId = $assign['location_id'];
                $batchId = null;

                if (!empty($assign['batch_code'])) {
                    $batch = ProductBatch::firstOrCreate(
                        [
                            'product_id' => $item->product_id, 
                            'batch_code' => $assign['batch_code']
                        ],
                        [
                            'expiry_date'      => $assign['expiry_date'] ?? null, 
                            'manufacture_date' => $assign['manufacture_date'] ?? null
                        ]
                    );
                    $batchId = $batch->id;
                    $this->inboundItemRepo->update($item->id, ['batch_id' => $batchId]);
                }

                $this->inventoryService->addStock([
                    'product_id'   => $item->product_id,
                    'location_id'  => $locationId,
                    'batch_id'     => $batchId,
                    'quantity'     => $item->quantity,
                    'reference_id' => $inboundId,
                    'note'         => 'Nhập kho từ phiếu Inbound #' . $inboundId
                ]);
            }

            return $order;
        });
    }

    public function cancelInboundOrder($inboundId)
    {
        return $this->inboundOrderRepo->update($inboundId, ['status' => 'cancelled']);
    }
}