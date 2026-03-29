<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerOrderController extends Controller
{
    /**
     * Hiển thị chi tiết đơn hàng
     */
    public function show(Order $order)
    {
        $this->authorize('view', $order);
        
        return view('customer.order.show', compact('order'));
    }

    /**
     * Hủy đơn hàng
     */
    public function cancel(Order $order)
    {
        $this->authorize('delete', $order);

        if (!in_array($order->status, ['pending', 'processing'])) {
            return back()->withErrors(['order' => 'Không thể hủy đơn hàng này!']);
        }

        $order->update(['status' => 'cancelled']);

        // Hoàn lại tồn kho
        foreach ($order->items as $item) {
            \App\Models\Inventory::where('product_id', $item->product_id)
                ->orderBy('id', 'desc')
                ->first()
                ->increment('quantity', $item->quantity);
        }

        return back()->with('success', 'Đã hủy đơn hàng!');
    }
}
