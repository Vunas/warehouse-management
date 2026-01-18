<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\WarehouseType;
use App\Services\WarehouseService; 
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    protected $warehouseService;

    public function __construct(WarehouseService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
        $this->authorizeResource(Warehouse::class, 'warehouse');
    }

    public function index()
    {
        // Lấy danh sách kho kèm loại và số lượng block
        $warehouses = Warehouse::with('type')->withCount('blocks')->get();
        return view('admin.warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        $types = WarehouseType::all();
        return view('admin.warehouses.create', compact('types'));
    }

    public function store(Request $request)
    {
        // Validate đơn giản (Nên tách ra WarehouseRequest)
        $data = $request->validate([
            'name' => 'required|string',
            'type_id' => 'required|exists:warehouse_types,id',
            'total_blocks' => 'required|integer|min:1',
            'slots_per_block' => 'required|integer|min:1', 
        ]);

        $this->warehouseService->createWarehouseWithBlocks($data);

        return redirect()->route('warehouses.index')->with('success', 'Tạo kho mới thành công!');
    }

    public function show(Warehouse $warehouse)
    {
        // Load quan hệ sâu để tính toán hiển thị
        $warehouse->load([
            'type',
            'blocks.contractBlocks.contract.customer', // Lấy thông tin người thuê
            'blocks.inventoryItems.product'            // Lấy thông tin hàng tồn
        ]);

        // Tính toán thống kê tổng quan
        $stats = [
            'total_capacity' => $warehouse->total_slots,
            'used_slots' => $warehouse->inventoryItems()->sum('slot_used'), // Sum từ InventoryItems thông qua relationship
            'occupied_blocks' => $warehouse->blocks->filter(function ($block) {
                return $block->inventoryItems->count() > 0;
            })->count(),
        ];

        $stats['usage_percent'] = $stats['total_capacity'] > 0
            ? round(($stats['used_slots'] / $stats['total_capacity']) * 100, 1)
            : 0;

        return view('admin.warehouses.show', compact('warehouse', 'stats'));
    }

    public function edit(Warehouse $warehouse)
    {
        $types = WarehouseType::all();
        return view('admin.warehouses.edit', compact('warehouse', 'types'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'status' => 'required|in:active,maintenance,locked'
        ]);

        $this->warehouseService->updateWarehouse($warehouse->id, $data);

        return redirect()->route('warehouses.index')->with('success', 'Cập nhật kho thành công!');
    }


    public function destroy(Warehouse $warehouse)
    {
        $this->warehouseService->deleteWarehouse($warehouse->id);
        return redirect()->route('warehouses.index')->with('success', 'Đã xóa kho.');
    }
}
