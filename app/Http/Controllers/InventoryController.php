<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Warehouse;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Exception;

class InventoryController extends Controller
{
    protected $inventoryService;

    // Tiêm Service vào Controller
    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    // API phục vụ AJAX chọn Nhà kho -> ra Vị trí (Chỉ lấy is_store = true)
    public function getLocationsApi($warehouseId)
    {
        $locations = Location::where('warehouse_id', $warehouseId)
            ->where('is_store', true)
            ->get();
        return response()->json($locations);
    }

    public function index(Request $request)
    {
        $inventories = Inventory::with(['product', 'location.warehouse'])
            ->orderBy('id', 'desc')
            ->paginate($request->get('per_page', 15));

        return view('admin.inventory.index', compact('inventories'));
    }

    public function create()
    {
        $warehouses = Warehouse::all(); // Trả về danh sách Kho thay vì Vị trí
        $products = Product::all();
        $batches = ProductBatch::all();
        return view('admin.inventory.form', compact('warehouses', 'products', 'batches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id'  => 'required|exists:products,id',
            'location_id' => 'required|exists:locations,id',
            'batch_id' => 'required|exists:product_batches,id',
            'quantity'    => 'required|integer|min:0',
        ]);

        try {
            // SỬ DỤNG SERVICE ĐỂ CỘNG DỒN NẾU TRÙNG, TẠO MỚI NẾU CHƯA CÓ
            $this->inventoryService->addStock(
                $request->product_id,
                $request->location_id,
                $request->batch_id,
                $request->quantity,
            );
            return redirect()->route('inventory.index')->with('success', 'Thêm / Cộng dồn tồn kho thành công!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $inventory = Inventory::with(['product', 'location.warehouse'])->findOrFail($id);
        return view('admin.inventory.show', compact('inventory'));
    }

    public function edit($id)
    {
        $inventory = Inventory::findOrFail($id);
        $warehouses = Warehouse::all();
        $products = Product::all();
        return view('admin.inventory.form', compact('inventory', 'warehouses', 'products'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $inventory = Inventory::findOrFail($id);

        // KHI SỬA, CHỈ CHO PHÉP SỬA SỐ LƯỢNG. KHÔNG CHO SỬA VỊ TRÍ VÀ SẢN PHẨM 
        // ĐỂ TRÁNH LỖI DUPLICATE 1062. (Muốn đổi vị trí phải dùng tính năng Chuyển Kho)
        $inventory->update([
            'quantity' => $request->quantity
        ]);

        return redirect()->route('inventory.index')->with('success', 'Cập nhật số lượng tồn kho thành công!');
    }

    public function destroy($id)
    {
        $inventory = Inventory::findOrFail($id);
        $inventory->delete();
        return back()->with('success', 'Xóa dòng tồn kho thành công!');
    }
}
