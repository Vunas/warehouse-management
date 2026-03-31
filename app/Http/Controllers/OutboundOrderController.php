<?php

namespace App\Http\Controllers;

use App\Models\OutboundOrder;
use App\Models\Order;
use App\Models\Warehouse;
use App\Models\Product;
use App\Services\OutboundOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class OutboundOrderController extends Controller
{
    protected $outboundService;

    public function __construct(OutboundOrderService $outboundService)
    {
        $this->outboundService = $outboundService;
    }

    // 🔥 ĐÂY LÀ HÀM BỊ THIẾU GÂY RA LỖI TẢI DỮ LIỆU KHO
    public function getInventoryApi($warehouseId)
    {
        try {
            $inventory = $this->outboundService->getInventoryForDropdown($warehouseId);
            return response()->json($inventory);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        $outbounds = OutboundOrder::with(['order', 'staff', 'warehouse'])->orderBy('id', 'desc')->paginate(15);
        return view('admin.outbounds.index', compact('outbounds'));
    }

    public function create()
    {
        $orders = Order::whereIn('status', ['paid', 'pending'])->get();
        $warehouses = Warehouse::all();
        $products = Product::all(); 

        return view('admin.outbounds.form', compact('orders', 'warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'warehouse_id'       => 'required|exists:warehouses,id',
            'type'               => 'required|in:sales,internal,adjustment',
            'order_id'           => 'required_if:type,sales|nullable|exists:orders,id',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|numeric|min:1',
        ]);

        try {
            $data = $request->only('warehouse_id', 'type', 'order_id', 'reason');
            $data['staff_id'] = Auth::id();

            $outbound = $this->outboundService->createOutboundOrder($data, $request->items);

            return redirect()->route('outbounds.show', $outbound->id)
                ->with('success', 'Tạo phiếu xuất và phân bổ hàng hóa thành công!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(OutboundOrder $outbound)
    {
        if ($outbound->status !== 'pending') {
            return back()->with('error', 'Chỉ được sửa phiếu đang chờ xuất!');
        }

        $outbound->load(['items.product', 'items.location', 'items.batch']);
        $orders = Order::whereIn('status', ['paid', 'pending'])->get();
        $warehouses = Warehouse::all();
        $products = Product::all();

        return view('admin.outbounds.form', compact('outbound', 'orders', 'warehouses', 'products'));
    }

    public function update(Request $request, OutboundOrder $outbound)
    {
        $request->validate([
            'warehouse_id'       => 'required|exists:warehouses,id',
            'type'               => 'required|in:sales,internal,adjustment',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|numeric|min:1',
        ]);

        try {
            $data = $request->only('warehouse_id', 'type', 'order_id', 'reason');
            $this->outboundService->updateOutboundOrder($outbound->id, $data, $request->items);

            return redirect()->route('outbounds.show', $outbound->id)->with('success', 'Cập nhật Phiếu xuất thành công!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(OutboundOrder $outbound)
    {
        $outbound->load(['items.product', 'items.location', 'items.batch', 'order', 'staff', 'warehouse']);
        return view('admin.outbounds.show', compact('outbound'));
    }

    public function complete($id)
    {
        try {
            $this->outboundService->completeOutboundOrder($id);
            return back()->with('success', 'Đã chốt xuất kho! Hàng thực tế đã được trừ.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->outboundService->cancelOutboundOrder($id);
            return back()->with('success', 'Đã hủy phiếu và nhả lại tồn kho dự kiến.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}