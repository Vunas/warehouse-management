<?php

namespace App\Http\Controllers;

use App\Http\Requests\Inventory\StoreInventoryRequest;
use App\Http\Requests\Inventory\UpdateInventoryRequest;
use App\Services\InventoryService;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Exception;

class InventoryController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function getLocationsApi($warehouseId)
    {
        $locations = $this->inventoryService->getStoreLocations($warehouseId);
        return response()->json($locations);
    }

    public function index(Request $request)
    {
        // Lấy toàn bộ tham số cần thiết cho Filter & Sort
        $filters = $request->only([
            'keyword', 
            'warehouse_id', 
            'stock_status', 
            'batch_code',
            'sort', // Thêm param sort
            'dir'   // Thêm param direction (chiều sắp xếp)
        ]);
        
        $inventories = $this->inventoryService->getPaginatedInventories($request->get('per_page', 15), $filters);
        
        $warehouses = Warehouse::all();

        return view('admin.inventory.index', compact('inventories', 'warehouses', 'filters'));
    }

    public function create()
    {
        $data = $this->inventoryService->getFormData();
        return view('admin.inventory.form', $data);
    }

    public function store(StoreInventoryRequest $request)
    {
        try {
            $this->inventoryService->addStock($request->validated());
            return redirect()->route('inventory.index')->with('success', 'Thêm / Cộng dồn tồn kho thành công!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $inventory = $this->inventoryService->getInventoryById($id);
        
        // Lấy thêm thống kê nhập xuất cho View Chi tiết
        $stats = $this->inventoryService->getInventoryStatistics($inventory);

        return view('admin.inventory.show', compact('inventory', 'stats'));
    }

    public function edit($id)
    {
        $inventory = $this->inventoryService->getInventoryById($id);
        $data = array_merge(['inventory' => $inventory], $this->inventoryService->getFormData());
        
        return view('admin.inventory.form', $data);
    }

    public function update(UpdateInventoryRequest $request, $id)
    {
        try {
            $this->inventoryService->updateStock($id, $request->validated());
            return redirect()->route('inventory.index')->with('success', 'Cập nhật số lượng tồn kho thành công!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->inventoryService->deleteStock($id);
            return back()->with('success', 'Xóa dòng tồn kho thành công!');
        } catch (Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}