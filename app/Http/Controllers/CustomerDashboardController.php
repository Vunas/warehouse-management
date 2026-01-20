<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\InboundService;
use App\Services\WarehouseService;
use App\Services\ContractService;
use App\Services\InventoryService;
use Illuminate\Support\Facades\Auth; // 👈 IMPORTANT

class CustomerDashboardController extends Controller
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
        $this->middleware('auth'); // protect route

        $this->inboundService = $inboundService;
        $this->warehouseService = $warehouseService;
        $this->contractService = $contractService;
        $this->inventoryService = $inventoryService;
    }

    public function index()
    {
        // current logged in user
        $user = Auth::user(); 

        // Stats
        $stats = [
            'pending_inbound' => $this->inboundService->countPending($user->id),
            'total_slots' => $this->warehouseService->getTotalCapacity(),
            'used_slots' => $this->inventoryService->getTotalUsedSlots(),
            'active_contracts' => $this->contractService->countActive(),
        ];

        $stats['free_slots'] = $stats['total_slots'] - $stats['used_slots'];

        $latestInbounds = $this->inboundService->getLatest(5);

        return view('customer.dashboard', compact('stats', 'latestInbounds'));
    }
}
