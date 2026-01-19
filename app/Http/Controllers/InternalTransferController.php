<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transfer\StoreInternalTransferRequest;
use App\Models\InternalTransfer;
use App\Services\InventoryService;
use App\Services\WarehouseService; 

class InternalTransferController extends Controller
{
    protected $inventoryService;
    protected $warehouseService;

    public function __construct(
        InventoryService $inventoryService,
        WarehouseService $warehouseService
    ) {
        $this->inventoryService = $inventoryService;
        $this->warehouseService = $warehouseService;
        // $this->authorizeResource(InternalTransfer::class, 'internal_transfer');
    }

    public function index()
    {
        // Service cần có hàm này (gọi repo->getTransfersPaginated)
        $transfers = $this->inventoryService->getTransfersPaginated();
        return view('admin.transfers.index', compact('transfers'));
    }

    public function create()
    {
        // Lấy danh sách Block khả dụng từ WarehouseService
        $blocks = $this->warehouseService->getAvailableBlocks();
        return view('admin.transfers.create', compact('blocks'));
    }

    public function store(StoreInternalTransferRequest $request)
    {
        $this->inventoryService->createTransfer($request->validated());

        return redirect()->route('transfers.index')
            ->with('success', 'Lệnh chuyển kho đã được tạo.');
    }

    public function complete(InternalTransfer $internalTransfer)
    {
        $this->inventoryService->executeTransfer($internalTransfer->id);
        
        return back()->with('success', 'Chuyển kho hoàn tất.');
    }
}