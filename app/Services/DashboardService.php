<?php
namespace App\Services;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\InboundOrder;
use App\Models\OutboundOrder;
use App\Models\StockTransfer;
use App\Models\Order;
use App\Models\CartItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    public function getSummaryStats()
    {
        // Thống kê tổng quan số lượng
        return [
            'total_products' => Product::count(),
            'total_inventory' => Inventory::sum('quantity') ?? 0,
            'pending_inbounds' => InboundOrder::where('status', 'pending')->count(),
            'pending_outbounds' => OutboundOrder::where('status', 'pending')->count(),
            'pending_transfers' => StockTransfer::where('status', 'pending')->count(),
        ];
    }

    public function getLowStockAlerts($threshold = 10)
    {
        // Lấy top 10 vị trí hàng sắp hết (dưới 10 sản phẩm)
        return Inventory::with(['product', 'location.warehouse'])
            ->where('quantity', '>', 0)
            ->where('quantity', '<=', $threshold)
            ->orderBy('quantity', 'asc')
            ->take(10)
            ->get();
    }

    public function getRecentPendingTasks()
    {
        // Lấy 5 phiếu xuất kho mới nhất đang chờ xử lý
        return OutboundOrder::with(['order', 'staff'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }

    // ===== CUSTOMER METHODS =====

    public function getCustomerProducts()
    {
        // Lấy tất cả sản phẩm hoạt động với thông tin tồn kho
        return Product::with(['brand', 'images', 'category'])
            ->where('is_active', true)
            ->get()
            ->map(function ($product) {
                $totalStock = Inventory::where('product_id', $product->id)->sum('quantity') ?? 0;
                $product->total_stock = $totalStock;
                $product->status = $totalStock > 0 ? 'Còn hàng' : 'Hết hàng';
                $product->status_color = $totalStock > 0 ? 'green' : 'red';
                return $product;
            });
    }

    public function getCustomerCartStats()
    {
        // Lấy thống kê giỏ hàng của khách hàng hiện tại
        $userId = Auth::id();
        if (!$userId) {
            return [
                'cart_items' => 0,
                'cart_total' => 0,
            ];
        }

        $cartItems = CartItem::where('user_id', $userId)->get();
        $cartTotal = $cartItems->sum(function ($item) {
            return ($item->product->price ?? 0) * $item->quantity;
        });

        return [
            'cart_items' => $cartItems->sum('quantity'),
            'cart_total' => $cartTotal,
        ];
    }

    public function getCustomerRecentOrders()
    {
        // Lấy các đơn hàng gần đây của khách hàng
        $userId = Auth::id();
        if (!$userId) {
            return [];
        }

        return Order::with(['items.product', 'payment'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }
}