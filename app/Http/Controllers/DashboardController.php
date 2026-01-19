<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\InboundService;
use App\Services\WarehouseService;
use App\Services\ContractService;
use App\Services\InventoryService;

class DashboardController extends Controller
{
    protected $inboundService;
    protected $warehouseService;
    protected $contractService;
    protected $inventoryService;

    public function __construct(
        InboundService $inboundService,
        WarehouseService $warehouseService,
        ContractService $contractService,
        InventoryService $inventoryService
    ) {
        $this->inboundService = $inboundService;
        $this->warehouseService = $warehouseService;
        $this->contractService = $contractService;
        $this->inventoryService = $inventoryService;
    }

    public function index()
    {
        // Bạn cần bổ sung các hàm thống kê này vào Service tương ứng
        $stats = [
            'pending_inbound' => $this->inboundService->countPending(),
            'total_slots' => $this->warehouseService->getTotalCapacity(),
            'used_slots' => $this->inventoryService->getTotalUsedSlots(),
            // free_slots = total - used (tính ở view hoặc controller)
            'active_contracts' => $this->contractService->countActive(),
        ];
        
        $stats['free_slots'] = $stats['total_slots'] - $stats['used_slots'];

        $latestInbounds = $this->inboundService->getLatest(5);

        return view('admin.dashboard', compact('stats', 'latestInbounds'));
    }
}