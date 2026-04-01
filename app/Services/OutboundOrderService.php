<?php

namespace App\Services;

use App\Repositories\Interfaces\OutboundOrderRepositoryInterface;
use App\Repositories\Interfaces\InventoryRepositoryInterface;
use App\Models\Order;
use App\Models\Warehouse;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Exception;

class OutboundOrderService
{
    protected $outboundRepo;
    protected $inventoryRepo;
    protected $inventoryService;

    public function __construct(
        OutboundOrderRepositoryInterface $outboundRepo,
        InventoryRepositoryInterface $inventoryRepo,
        InventoryService $inventoryService
    ) {
        $this->outboundRepo = $outboundRepo;
        $this->inventoryRepo = $inventoryRepo;
        $this->inventoryService = $inventoryService;
    }

    public function getPaginatedOutbounds($perPage = 15)
    {
        return $this->outboundRepo->getPaginatedOrders($perPage);
    }

    public function getFormData()
    {
        return [
            'orders'     => Order::whereIn('status', ['paid', 'pending'])->get(),
            'warehouses' => Warehouse::all(),
            'products'   => Product::all()
        ];
    }

    public function getShowData($id)
    {
        return $this->outboundRepo->findById($id, ['*'], ['items.product', 'items.location', 'items.batch', 'order', 'staff', 'warehouse']);
    }

    public function getInventoryForDropdown($warehouseId)
    {
        return $this->inventoryRepo->all(['*'], ['product', 'location', 'batch'])
            ->filter(function ($inv) use ($warehouseId) {
                return $inv->location->warehouse_id == $warehouseId && ($inv->quantity - $inv->reserved_quantity) > 0;
            })
            ->sortBy(function($inventory) {
                return optional($inventory->batch)->expiry_date ?: '9999-12-31';
            })
            ->values();
    }

    public function createOutboundOrder(array $data, array $items)
    {
        $data['status'] = 'pending'; 

        return DB::transaction(function () use ($data, $items) {
            $outbound = $this->outboundRepo->create($data);

            foreach ($items as $item) {
                if (!empty($item['product_id']) && !empty($item['location_id']) && !empty($item['quantity'])) {
                    $batchId = $item['batch_id'] ?? null;
                    
                    $inventory = $this->inventoryRepo->getStock($item['product_id'], $item['location_id'], $batchId);
                    $available = $inventory ? ($inventory->quantity - $inventory->reserved_quantity) : 0;
                    
                    if ($item['quantity'] > $available) {
                        throw new Exception("Sản phẩm ID {$item['product_id']} tại vị trí ID {$item['location_id']} không đủ hàng tồn (Chỉ còn {$available}).");
                    }

                    $outbound->items()->create([
                        'product_id'  => $item['product_id'],
                        'location_id' => $item['location_id'],
                        'batch_id'    => $batchId,
                        'quantity'    => $item['quantity'],
                    ]);
                }
            }
            return $outbound;
        });
    }

    public function updateOutboundOrder($id, array $data, array $items)
    {
        $outbound = $this->outboundRepo->findById($id);
        if ($outbound->status !== 'pending') {
            throw new Exception("Chỉ được phép sửa phiếu đang chờ xuất.");
        }

        return DB::transaction(function () use ($outbound, $data, $items) {
            $this->outboundRepo->update($outbound->id, $data);
            $outbound->items()->delete(); 

            foreach ($items as $item) {
                if (!empty($item['product_id']) && !empty($item['location_id']) && !empty($item['quantity'])) {
                    $outbound->items()->create([
                        'product_id'  => $item['product_id'],
                        'location_id' => $item['location_id'],
                        'batch_id'    => $item['batch_id'] ?? null,
                        'quantity'    => $item['quantity'],
                    ]);
                }
            }
            return $outbound;
        });
    }

    public function completeOutboundOrder($outboundId)
    {
        $outbound = $this->outboundRepo->findById($outboundId, ['*'], ['items']);

        if ($outbound->status !== 'pending') {
            throw new Exception("Phiếu xuất kho này đã được xử lý.");
        }

        return DB::transaction(function () use ($outbound) {
            $this->outboundRepo->update($outbound->id, ['status' => 'completed']);
            $isSales = ($outbound->type === 'sales');

            foreach ($outbound->items as $item) {
                // ĐÃ FIX: Chuyển trách nhiệm trừ kho và ghi Log sang InventoryService
                $this->inventoryService->deductExactStock(
                    $item->product_id,
                    $item->location_id,
                    $item->batch_id,
                    $item->quantity,
                    $isSales,
                    $outbound->id,
                    "Xuất kho từ phiếu Outbound #" . $outbound->id
                );
            }

            if ($isSales && $outbound->order_id) {
                Order::where('id', $outbound->order_id)->update(['status' => 'shipping']);
            }

            return $outbound;
        });
    }

    public function cancelOutboundOrder($outboundId)
    {
        return $this->outboundRepo->update($outboundId, ['status' => 'cancelled']);
    }
}