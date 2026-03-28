<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Services\WarehouseService;
use Illuminate\Http\Request;
use App\Http\Requests\Warehouse\StoreWarehouseRequest;
use App\Http\Requests\Warehouse\UpdateWarehouseRequest;
use Exception;

class WarehouseController extends Controller
{
    protected $warehouseService;

    public function __construct(WarehouseService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
        $this->authorizeResource(Warehouse::class, 'warehouse');
    }

    public function index(Request $request)
    {
        $warehouses = $this->warehouseService->getPaginatedWarehouses($request->get('per_page', 15));
        return view('admin.warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        return view('admin.warehouses.form');
    }


    public function store(StoreWarehouseRequest $request)
    {
        try {
            $this->warehouseService->createWarehouse($request->validated());
            return redirect()->route('warehouses.index')->with('success', 'Tạo kho thành công!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
    public function edit(Warehouse $warehouse)
    {
        return view('admin.warehouses.form', compact('warehouse'));
    }

    public function update(UpdateWarehouseRequest $request, Warehouse $warehouse)
    {
        try {
            $this->warehouseService->updateWarehouse($warehouse->id, $request->validated());
            return redirect()->route('warehouses.index')->with('success', 'Cập nhật thành công!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function destroy(Warehouse $warehouse)
    {
        try {
            $this->warehouseService->deleteWarehouse($warehouse->id);
            return redirect()->route('warehouses.index')->with('success', 'Đã xóa kho!');
        } catch (Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}
