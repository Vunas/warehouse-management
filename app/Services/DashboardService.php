<?php
namespace App\Services;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\InboundOrder;
use App\Models\OutboundOrder;
use App\Models\StockTransfer;
use Illuminate\Support\Facades\DB;

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
}