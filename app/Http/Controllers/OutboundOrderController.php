<?php

namespace App\Http\Controllers;

use App\Models\OutboundOrder;
use App\Models\Order;
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
        $this->authorizeResource(OutboundOrder::class, 'outbound');
    }

    public function index(Request $request)
    {
        $outbounds = $this->outboundService->getPaginatedOutbounds($request->get('per_page', 15));
        return view('admin.outbounds.index', compact('outbounds'));
    }

    public function create()
    {
        $orders = Order::where('status', 'paid')->orWhere('status', 'pending')->get();
        return view('admin.outbounds.create', compact('orders'));
    }

    public function store(Request $request)
    {
        $request->validate(['order_id' => 'required|exists:orders,id']);
        try {
            $data = $request->only('order_id');
            $data['staff_id'] = Auth::id();
            $outbound = $this->outboundService->createOutboundOrder($data);
            return redirect()->route('outbounds.show', $outbound->id)->with('success', 'Tạo phiếu xuất kho thành công!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(OutboundOrder $outbound)
    {
        $outbound->load(['items.product', 'order', 'staff']);
        return view('admin.outbounds.show', compact('outbound'));
    }

    public function complete($id)
    {
        $this->authorize('approve_outbounds', OutboundOrder::class); // Check quyền duyệt xuất kho
        try {
            $this->outboundService->completeOutboundOrder($id);
            return back()->with('success', 'Đã hoàn tất phiếu xuất. Kho đã tự động trừ theo FIFO!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel($id)
    {
        $this->authorize('approve_outbounds', OutboundOrder::class);
        $this->outboundService->cancelOutboundOrder($id);
        return back()->with('success', 'Đã hủy phiếu xuất kho.');
    }
}