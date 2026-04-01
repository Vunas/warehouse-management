<?php

namespace App\Http\Controllers;

use App\Http\Requests\Inventory\StoreInventoryRequest;
use App\Http\Requests\Inventory\UpdateInventoryRequest;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Exception;

class InventoryController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    // API phục vụ AJAX
    public function getLocationsApi($warehouseId)
    {
        $locations = $this->inventoryService->getStoreLocations($warehouseId);
        return response()->json($locations);
    }

    public function index(Request $request)
    {
        $inventories = $this->inventoryService->getPaginatedInventories($request->get('per_page', 15));
        return view('admin.inventory.index', compact('inventories'));
    }

    public function create()
    {
        $data = $this->inventoryService->getFormData();
        return view('admin.inventory.form', $data);
    }

    public function store(StoreInventoryRequest $request)
    {
        try {
            // Truyền mảng dữ liệu đã được validate an toàn vào Service
            $this->inventoryService->addStock($request->validated());
            
            return redirect()->route('inventory.index')->with('success', 'Thêm / Cộng dồn tồn kho thành công!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $inventory = $this->inventoryService->getInventoryById($id);
        return view('admin.inventory.show', compact('inventory'));
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