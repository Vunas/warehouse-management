<?php

namespace App\Http\Controllers;

use App\Models\InboundOrder;
use App\Models\InboundItem;
use App\Models\Location;
use App\Models\Supplier;
use App\Models\Product;
use App\Services\InboundOrderService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;

class InboundOrderController extends Controller
{
    protected $inboundOrderService;

    public function __construct(InboundOrderService $inboundOrderService)
    {
        $this->inboundOrderService = $inboundOrderService;
    }

    public function index(Request $request)
    {
        $inbounds = $this->inboundOrderService->getPaginatedInbounds($request->get('per_page', 15));
        return view('admin.inbounds.index', compact('inbounds'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        return view('admin.inbounds.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate(['supplier_id' => 'required|exists:suppliers,id']);
        try {
            $data = $request->only('supplier_id');
            $data['staff_id'] = Auth::id();
            $inbound = $this->inboundOrderService->createInboundOrder($data);
            return redirect()->route('inbounds.show', $inbound->id)->with('success', 'Khởi tạo phiếu nhập kho thành công!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $inbound = InboundOrder::with(['items.product', 'supplier', 'staff'])->findOrFail($id);
        $locations = Location::where('is_store', true)->get(); 
        $products = Product::where('is_active', 1)->get();
        return view('admin.inbounds.show', compact('inbound', 'locations', 'products'));
    }

    public function addItem(Request $request, $id)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);
        try {
            $inbound = InboundOrder::findOrFail($id);
            if ($inbound->status !== 'pending') throw new Exception("Không thể thêm SP vào phiếu đã duyệt.");

            // Kiểm tra xem SP này đã có trong phiếu chưa, nếu có thì cộng dồn số lượng
            $existingItem = InboundItem::where('inbound_id', $id)->where('product_id', $request->product_id)->first();
            
            if ($existingItem) {
                $existingItem->update([
                    'quantity' => $existingItem->quantity + $request->quantity,
                    'price' => $request->price // Cập nhật giá mới nhất nếu có thay đổi
                ]);
            } else {
                InboundItem::create([
                    'inbound_id' => $id, 
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity, 
                    'price' => $request->price,
                ]);
            }

            return back()->with('success', 'Đã thêm sản phẩm vào phiếu nhập.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // THÊM HÀM NÀY ĐỂ CẬP NHẬT TRỰC TIẾP
    public function updateItem(Request $request, $id, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        try {
            $inbound = InboundOrder::findOrFail($id);
            if ($inbound->status !== 'pending') throw new Exception("Phiếu đã chốt, không thể chỉnh sửa.");

            $item = InboundItem::findOrFail($itemId);
            $item->update([
                'quantity' => $request->quantity,
                'price' => $request->price,
            ]);

            return back()->with('success', 'Đã cập nhật số lượng và đơn giá.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function removeItem($id, $itemId)
    {
        try {
            $item = InboundItem::findOrFail($itemId);
            if ($item->inbound_order->status !== 'pending') throw new Exception("Phiếu đã khóa, không thể xóa.");
            $item->delete();
            return back()->with('success', 'Đã xóa sản phẩm khỏi phiếu.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function complete(Request $request, $id)
    {
        $request->validate([
            'location_assignments' => 'required|array',
            'location_assignments.*' => 'required|exists:locations,id'
        ]);
        try {
            $this->inboundOrderService->completeInboundOrder($id, $request->location_assignments);
            return back()->with('success', 'Hoàn tất nhập kho, tồn kho đã được cộng!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel($id)
    {
        try {
            $this->inboundOrderService->cancelInboundOrder($id);
            return back()->with('success', 'Đã hủy phiếu nhập kho!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}