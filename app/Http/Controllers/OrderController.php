<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        $orders = $this->orderService->getPaginatedOrders($request->get('per_page', 15));
        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = $this->orderService->getOrderById($id);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Dành cho Khách hàng: Đặt hàng từ Giỏ hàng (Checkout)
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id'
        ]);

        try {
            $userId = Auth::id();
            $order = $this->orderService->placeOrder($userId, $request->address_id);
            
            return redirect()->route('orders.success', $order->id)->with('success', 'Đặt hàng thành công!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Dành cho Admin/Nhân viên: Cập nhật trạng thái đơn hàng
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,shipping,completed,cancelled'
        ]);

        try {
            $this->orderService->updateOrderStatus($id, $request->status);
            return back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
        } catch (Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}