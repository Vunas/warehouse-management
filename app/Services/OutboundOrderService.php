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

    public function getInventoryForDropdown($warehouseId, string $outboundType = 'sales')
    {
        return $this->inventoryRepo->all(['*'], ['product', 'location', 'batch'])
            ->filter(function ($inv) use ($warehouseId, $outboundType) {
                if ($inv->location->warehouse_id != $warehouseId) return false;

                // Nếu là đơn bán hàng: Cho chọn tất cả kệ có tồn kho thực tế (bù trừ reserved sau)
                if ($outboundType === 'sales') {
                    return $inv->quantity > 0;
                }

                // Nếu là xuất nội bộ/khác: Chỉ cho phép chọn hàng khả dụng (không đụng vào hàng khách đã đặt)
                return ($inv->quantity - $inv->reserved_quantity) > 0;
            })
            ->sortBy(function ($inventory) {
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
                if ($isSales) {
                    // Khóa dòng tồn kho tại vị trí mà nhân viên kho CHỌN TAY thực tế
                    $actualInv = $this->inventoryRepo->getLockedStock($item->product_id, $item->location_id, $item->batch_id);

                    // Nếu ô kệ chọn tay này không đủ lượng reserved, ta mới đi gom từ chỗ khác về
                    if ($actualInv->reserved_quantity < $item->quantity) {
                        $shortage = $item->quantity - $actualInv->reserved_quantity;

                        // Đi tìm các ô kệ khác của sản phẩm này đang có reserved thừa
                        $otherReservations = $this->inventoryRepo->getReservedStockByProduct($item->product_id);

                        foreach ($otherReservations as $otherInv) {
                            if ($shortage <= 0) break;
                            if ($otherInv->id === $actualInv->id) continue;

                            $currentOther = $this->inventoryRepo->getLockedById($otherInv->id);
                            $takeReserved = min($currentOther->reserved_quantity, $shortage);

                            // Nhả reserved ở kệ thừa
                            $this->inventoryRepo->update($currentOther->id, [
                                'reserved_quantity' => $currentOther->reserved_quantity - $takeReserved
                            ]);

                            // Đập cục reserved đó vào kệ thực tế đang xuất
                            $actualInv->reserved_quantity += $takeReserved;
                            $shortage -= $takeReserved;
                        }

                        // Cập nhật lại giá trị reserved_quantity sau khi đã gom (nếu có gom được)
                        $this->inventoryRepo->update($actualInv->id, [
                            'reserved_quantity' => $actualInv->reserved_quantity
                        ]);

                        if ($shortage > 0) {
                            $this->inventoryRepo->update($actualInv->id, [
                                'reserved_quantity' => $actualInv->reserved_quantity + $shortage
                            ]);
                        }
                    }
                }

                // Gọi hàm trừ kho chuẩn chỉ
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
