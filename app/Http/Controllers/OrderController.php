<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use App\Services\AddressService;
use Illuminate\Http\Request;
use Exception;

class OrderController extends Controller
{
    protected $orderService;
    protected $addressService;

    public function __construct(OrderService $orderService, AddressService $addressService)
    {
        $this->orderService = $orderService;
        $this->addressService = $addressService;
    }

    public function index(Request $request)
    {
        $orders = $this->orderService->getPaginatedOrders($request->get('per_page', 15));
        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = $this->orderService->getOrderById($id);
        $address = $order->address_id ? $this->addressService->getAddressById($order->address_id) : null;

        return view('admin.orders.show', compact('order', 'address'));
    }

    // TỪ CHỐI NGUYÊN ĐƠN HÀNG
    public function rejectOrder(Request $request, $orderId)
    {
        $request->validate(['reason' => 'required|string|max:255']);

        try {
            $this->orderService->rejectOrder($orderId, $request->reason);
            return back()->with('success', 'Đã từ chối toàn bộ đơn hàng! Hàng hóa đã được hoàn về giỏ của khách và tồn kho đã được nhả giữ chỗ.');
        } catch (Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, $id)
    {
        // Danh sách trạng thái chuẩn hóa
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipping,completed,cancelled'
        ]);

        try {
            $this->orderService->updateOrderStatus($id, $request->status);
            return back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
        } catch (Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}