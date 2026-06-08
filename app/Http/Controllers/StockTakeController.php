<?php

namespace App\Http\Controllers;

use App\Models\StockTake;
use App\Models\Warehouse;
use App\Services\StockTakeService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;

class StockTakeController extends Controller
{
    protected $stockTakeService;

    public function __construct(StockTakeService $stockTakeService)
    {
        $this->stockTakeService = $stockTakeService;
    }

    public function index(Request $request)
    {
        $stockTakes = StockTake::with(['warehouse', 'staff'])->latest('id')->paginate(15);
        $warehouses = Warehouse::all();
        return view('admin.stock_takes.index', compact('stockTakes', 'warehouses'));
    }

    public function create()
    {
        $warehouses = Warehouse::all();
        return view('admin.stock_takes.create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'notes' => 'nullable|string'
        ]);

        try {
            $data = $request->only(['warehouse_id', 'notes']);
            $data['staff_id'] = Auth::id();

            $stockTake = $this->stockTakeService->createDraft($data);

            return redirect()->route('stock_takes.show', $stockTake->id)->with('success', 'Đã khởi tạo phiếu kiểm kê nháp.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function addUnexpected(Request $request, $id)
    {
        try {
            $this->stockTakeService->addUnexpectedItemToStockTake($id, $request->all());
            return back()->with('success', 'Đã thêm mặt hàng phát sinh vào phiếu đếm!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $stockTake = StockTake::with([
            'warehouse',
            'staff',
            'items.product',
            'items.location',
            'items.batch'
        ])->findOrFail($id);
        $products = \App\Models\Product::all();
        $locations = \App\Models\Location::where('warehouse_id', $stockTake->warehouse_id and 'is_store', true)->get();
        $batches = \App\Models\ProductBatch::all();
        return view('admin.stock_takes.show', compact('stockTake', 'products', 'locations', 'batches'));
    }

    public function start($id)
    {
        try {
            $this->stockTakeService->startCounting($id);
            return back()->with('success', 'Đã chụp tồn kho dự kiến. Bắt đầu quá trình đếm!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function updateBulk(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:save,complete',
            'items' => 'required|array',
            'items.*.counted_quantity' => 'nullable|integer|min:0',
            'items.*.reason' => 'nullable|string'
        ]);

        try {
            // 1. Dù ấn nút nào cũng PHẢI LƯU TẠM DỮ LIỆU ĐÃ NHẬP trước
            $this->stockTakeService->saveCountBulk($id, $request->items);

            // 2. Nếu nút được ấn là "Hoàn tất" -> Gọi tiếp hàm chốt sổ
            if ($request->action === 'complete') {
                $this->stockTakeService->complete($id, Auth::id());
                return back()->with('success', 'Đã lưu dữ liệu đếm VÀ HOÀN TẤT KIỂM KÊ. Sổ kho đã được điều chỉnh!');
            }

            // Nếu chỉ là lưu tạm
            return back()->with('success', 'Đã lưu tạm kết quả đếm thành công.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel($id)
    {
        try {
            $this->stockTakeService->cancel($id);
            return back()->with('success', 'Đã hủy phiếu kiểm kê.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
