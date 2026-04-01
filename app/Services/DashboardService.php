<?php
namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
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

    public function getCustomerProducts($search = null, $filters = [])
    {
        // Lấy tất cả sản phẩm hoạt động với thông tin tồn kho
        $query = Product::with(['brand', 'images', 'category'])
            ->where('is_active', true);

        // Tìm kiếm theo tên sản phẩm (tương đối)
        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Lọc theo danh mục
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Lọc theo thương hiệu
        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        // Lọc theo giá từ
        if (!empty($filters['price_from'])) {
            $query->where('price', '>=', $filters['price_from']);
        }

        // Lọc theo giá đến
        if (!empty($filters['price_to'])) {
            $query->where('price', '<=', $filters['price_to']);
        }

        // Lọc theo trạng thái hàng
        if (isset($filters['stock_status']) && $filters['stock_status'] !== '') {
            $stockStatus = $filters['stock_status'];
            if ($stockStatus === 'in_stock') {
                // Có hàng - sẽ lọc ở phần map
            } elseif ($stockStatus === 'out_of_stock') {
                // Hết hàng - sẽ lọc ở phần map
            }
        }

        return $query->get()
            ->map(function ($product) {
                $totalStock = Inventory::where('product_id', $product->id)->sum('quantity') ?? 0;
                $product->total_stock = $totalStock;
                $product->status = $totalStock > 0 ? 'Còn hàng' : 'Hết hàng';
                $product->status_color = $totalStock > 0 ? 'green' : 'red';
                return $product;
            })
            ->when(isset($filters['stock_status']) && $filters['stock_status'] === 'in_stock', function($products) {
                return $products->filter(fn($p) => $p->total_stock > 0);
            })
            ->when(isset($filters['stock_status']) && $filters['stock_status'] === 'out_of_stock', function($products) {
                return $products->filter(fn($p) => $p->total_stock <= 0);
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

    public function getCategories()
    {
        // Lấy danh sách tất cả danh mục
        return Category::orderBy('name')->get();
    }

    public function getBrands()
    {        // Lấy danh sách tất cả thương hiệu
        return Brand::orderBy('name')->get();}
}