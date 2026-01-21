<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\InboundService;
use App\Services\OutboundService;
use App\Services\WarehouseService;
use App\Services\ContractService;
use App\Services\InventoryService;
use Illuminate\Support\Facades\Auth; // 👈 IMPORTANT

class CustomerDashboardController extends Controller
{
    protected $inboundService;
    protected $outboundService;
    protected $warehouseService;
    protected $contractService;
    protected $inventoryService;

    public function __construct(
        InboundService $inboundService,
        OutboundService $outboundService,
        WarehouseService $warehouseService,
        ContractService $contractService,
        InventoryService $inventoryService
    ) {
        $this->middleware('auth'); // protect route

        $this->inboundService = $inboundService;
        $this->outboundService = $outboundService;
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
            'pending_outbound' => $this->outboundService->countPending($user->id),
            'used_slots' => $this->inventoryService->getTotalUsedSlots(),
            'active_contracts' => $this->contractService->countActive($user->id),
        ];

        $stats['free_slots'] =  $stats['used_slots'];

        $latestInbounds = $this->inboundService->getLatest(5,$user->id);

        return view('customer.dashboard', compact('stats', 'latestInbounds'));
    }
}
