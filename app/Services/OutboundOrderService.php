<?php

namespace App\Services;

use App\Repositories\Interfaces\OutboundOrderRepositoryInterface;
use App\Repositories\Interfaces\InventoryRepositoryInterface;
use App\Models\Order;
use App\Models\OrderItem;
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

    // TÍNH NĂNG MỚI: Lấy item của order
    public function getOrderItemsApi($orderId)
    {
        // Lấy chi tiết món hàng của order
        return OrderItem::where('order_id', $orderId)->get(['product_id', 'quantity']);
    }

    public function getPaginatedOutbounds($perPage = 15)
    {
        return $this->outboundRepo->getPaginatedOrders($perPage);
    }

    public function getFormData()
    {
        return [
            // Chỉ lấy Order chưa hoàn thành / chưa xuất xong
            'orders'     => Order::whereIn('status', ['paid', 'pending', 'processing', 'confirmed'])->get(),
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
                // FEFO: Lấy Tồn kho On Hand trừ đi Hàng đã giữ chỗ (Reserved)
                return $inv->location->warehouse_id == $warehouseId && ($inv->quantity - $inv->reserved_quantity) > 0;
            })
            ->sortBy(function($inventory) {
                // Ưu tiên HSD gần nhất (FEFO)
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
            
            // Cập nhật Order thành đang xử lý kho
            if ($data['type'] === 'sales' && !empty($data['order_id'])) {
                Order::where('id', $data['order_id'])->update(['status' => 'processing']);
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
                // Trừ kho thật
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
        return DB::transaction(function () use ($outboundId) {
            $outbound = $this->outboundRepo->findById($outboundId);
            $this->outboundRepo->update($outboundId, ['status' => 'cancelled']);

            // Trả order về confirmed nếu cancel phiếu xuất
            if ($outbound->type === 'sales' && $outbound->order_id) {
                Order::where('id', $outbound->order_id)->update(['status' => 'confirmed']);
            }

            return $outbound;
        });
    }
}