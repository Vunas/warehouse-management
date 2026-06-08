<?php

namespace App\Services;

use App\Repositories\Interfaces\StockTransferRepositoryInterface;
use App\Repositories\Interfaces\TransferItemRepositoryInterface;
use App\Repositories\Interfaces\InventoryRepositoryInterface;
use App\Models\Warehouse;
use App\Models\Location;
use Illuminate\Support\Facades\DB;
use Exception;

class StockTransferService
{
    protected $transferRepo;
    protected $transferItemRepo;
    protected $inventoryRepo;
    protected $inventoryService;

    public function __construct(
        StockTransferRepositoryInterface $transferRepo,
        TransferItemRepositoryInterface $transferItemRepo,
        InventoryRepositoryInterface $inventoryRepo,
        InventoryService $inventoryService
    ) {
        $this->transferRepo = $transferRepo;
        $this->transferItemRepo = $transferItemRepo;
        $this->inventoryRepo = $inventoryRepo;
        $this->inventoryService = $inventoryService;
    }

    public function getPaginatedTransfers(array $filters, int $perPage)
    {
        return $this->transferRepo->filterAndPaginate($filters, $perPage);
    }

    public function getCreateData()
    {
        return ['warehouses' => Warehouse::all()];
    }

    public function getShowData($id)
    {
        $transfer = $this->transferRepo->findById($id, ['*'], [
            'items.product',
            'items.inventory.location',
            'items.batch',
            'staff',
            'fromWarehouse',
            'toWarehouse'
        ]);

        // Tính tổng tồn kho khả dụng ở kho nguồn
        $productsInStock = $this->inventoryRepo->all(['*'], ['product', 'location'])
            ->where('location.warehouse_id', $transfer->from_warehouse_id)
            ->where('quantity', '>', 0)
            ->groupBy('product_id')
            ->map(function ($items) {
                return (object)[
                    'product_id' => $items->first()->product_id,
                    'product' => $items->first()->product,
                    'total_available' => $items->sum('quantity')
                ];
            });

        $productIdsInTransfer = $transfer->items->pluck('product_id')->unique()->toArray();
        
        $availableInventories = [];
        if (!empty($productIdsInTransfer)) {
            $inventories = $this->inventoryRepo->all(['*'], ['location', 'batch'])
                ->whereIn('product_id', $productIdsInTransfer)
                ->where('location.warehouse_id', $transfer->from_warehouse_id)
                ->where('quantity', '>', 0);
                
            $availableInventories = $inventories->groupBy('product_id');
        }

        $toLocations = Location::where('warehouse_id', $transfer->to_warehouse_id)
            ->where('is_store', true)
            ->get();

        return compact('transfer', 'productsInStock', 'toLocations', 'availableInventories');
    }

    public function createTransfer(array $data)
    {
        $data['status'] = 'pending';
        return $this->transferRepo->create($data);
    }

    public function autoAllocateAndAddItems($transferId, $productId, $quantityRequested)
    {
        $transfer = $this->transferRepo->findById($transferId);

        // Gọi repo để lấy danh sách tồn kho theo quy tắc FEFO (Hết hạn trước xuất trước)
        $inventories = $this->inventoryRepo->getPrioritizedStock($productId, $transfer->from_warehouse_id);

        $remaining = $quantityRequested;

        return DB::transaction(function () use ($transferId, $productId, $inventories, &$remaining, $quantityRequested) {
            foreach ($inventories as $inv) {
                if ($remaining <= 0) break;

                $take = min($inv->quantity, $remaining);

                $this->transferItemRepo->create([
                    'transfer_id'    => $transferId,
                    'inventory_id'   => $inv->id,
                    'product_id'     => $productId,
                    'batch_id'       => $inv->batch_id,
                    'quantity'       => $take,
                    'to_location_id' => null 
                ]);

                $remaining -= $take;
            }

            if ($remaining > 0) {
                throw new Exception("Kho nguồn hiện tại chỉ còn " . ($quantityRequested - $remaining) . " sản phẩm khả dụng. Không đủ {$quantityRequested} SP.");
            }
        });
    }

    public function updateBulkItems($transferId, array $itemsData)
    {
        $transfer = $this->transferRepo->findById($transferId, ['*'], ['items']);

        if ($transfer->status !== 'pending') {
            throw new Exception("Phiếu đã được xử lý hoặc đã hủy, không thể lưu.");
        }

        return DB::transaction(function () use ($transfer, $itemsData) {
            $usedInventories = []; 

            foreach ($itemsData as $itemId => $data) {
                $item = $transfer->items->where('id', $itemId)->first();
                if (!$item) continue;

                $inventoryId = $data['inventory_id'];
                $quantity = $data['quantity'];

                if (in_array($inventoryId, $usedInventories)) {
                    throw new Exception("Lỗi: SP-{$item->product_id} đang bị chọn rút từ cùng MỘT kệ nhiều lần. Vui lòng gộp số lượng.");
                }
                $usedInventories[] = $inventoryId;

                $inventory = $this->inventoryRepo->findById($inventoryId);
                if (!$inventory || $inventory->quantity < $quantity) {
                    throw new Exception("Kệ rút hàng của SP-{$item->product_id} không đủ số lượng (Chỉ còn {$inventory->quantity}).");
                }

                $this->transferItemRepo->update($item->id, [
                    'inventory_id'   => $inventoryId,
                    'quantity'       => $quantity,
                    'to_location_id' => $data['to_location_id'] ?? null,
                ]);
            }

            return true;
        });
    }

    public function removeItem($itemId)
    {
        return $this->transferItemRepo->delete($itemId);
    }

    public function completeTransfer($transferId)
    {
        $transfer = $this->transferRepo->findById($transferId, ['*'], ['items.inventory']);

        if ($transfer->status !== 'pending') {
            throw new Exception("Phiếu đã được xử lý hoặc đã hủy.");
        }

        return DB::transaction(function () use ($transfer) {
            foreach ($transfer->items as $item) {
                if (!$item->to_location_id) throw new Exception("SP-{$item->product_id} chưa chọn Kệ cất hàng.");
                if (!$item->inventory_id) throw new Exception("SP-{$item->product_id} chưa chọn Kệ rút hàng.");

                // Lock for update
                $currentInventory = $this->inventoryRepo->getLockedById($item->inventory_id);
                
                if (!$currentInventory || $currentInventory->quantity < $item->quantity) {
                    throw new Exception("Kệ rút hàng của SP-{$item->product_id} hiện không đủ số lượng.");
                }
            }

            foreach ($transfer->items as $item) {
                // Trừ đích danh và ghi Log Outbound (Dùng hàm mới tạo ở bài trước)
                $this->inventoryService->deductExactStock(
                    $item->product_id,
                    $item->inventory->location_id,
                    $item->batch_id,
                    $item->quantity,
                    false, // isSales = false (Nội bộ)
                    $transfer->id,
                    "Rút hàng chuyển kho (TRF-{$transfer->id})"
                );

                // Cộng đích danh và ghi Log Inbound
                $this->inventoryService->addStock([
                    'product_id'   => $item->product_id,
                    'location_id'  => $item->to_location_id,
                    'batch_id'     => $item->batch_id,
                    'quantity'     => $item->quantity,
                    'reference_id' => $transfer->id,
                    'note'         => "Cất hàng chuyển kho (TRF-{$transfer->id})"
                ]);
            }

            return $this->transferRepo->update($transfer->id, ['status' => 'completed']);
        });
    }

    public function cancelTransfer($transferId)
    {
        return $this->transferRepo->update($transferId, ['status' => 'cancelled']);
    }
}