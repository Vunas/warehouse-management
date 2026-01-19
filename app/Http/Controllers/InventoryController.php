<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\InventoryService;
use App\Services\WarehouseService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    protected $inventoryService;
    protected $warehouseService;

    public function __construct(
        InventoryService $inventoryService,
        WarehouseService $warehouseService
    ) {
        $this->inventoryService = $inventoryService;
        $this->warehouseService = $warehouseService;
    }

    public function index(Request $request)
    {
        // Chuyển toàn bộ logic filter vào Service
        // Service sẽ gọi Repository để build query
        $items = $this->inventoryService->searchInventory($request->all());
        
        // Lấy danh sách kho cho filter dropdown
        $warehouses = $this->warehouseService->getWarehouseSelection();

        return view('admin.inventory.index', compact('items', 'warehouses'));
    }
}