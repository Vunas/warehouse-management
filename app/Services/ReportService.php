<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportService
{
    /**
     * KPI tổng quan
     */
    public function getKpiSummary($startDate, $endDate)
    {
        $totalRevenue = DB::table('orders')
            ->whereIn('status', ['paid', 'completed'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_price');

        $totalOrders = DB::table('orders')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $totalInbounds = DB::table('inbound_orders')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // ⚠️ Fix: tránh lỗi count sai khi dùng groupBy
        $lowStockCount = DB::table(DB::raw("
            (
                SELECT 
                    inventory.product_id,
                    SUM(inventory.quantity) as total_qty,
                    MAX(COALESCE(product_alerts.stock_threshold, 10)) as threshold
                FROM inventory
                LEFT JOIN product_alerts 
                    ON inventory.product_id = product_alerts.product_id
                GROUP BY inventory.product_id
                HAVING total_qty <= threshold
            ) as sub
        "))->count();

        return [
            'total_revenue' => $totalRevenue,
            'total_orders'  => $totalOrders,
            'total_inbounds'=> $totalInbounds,
            'low_stock'     => $lowStockCount
        ];
    }

    /**
     * Biểu đồ doanh thu
     */
    public function getRevenueChartData($startDate, $endDate)
    {
        $data = DB::table('orders')
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as total')
            ->whereIn('status', ['paid', 'completed'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $data->pluck('date')->map(fn($d) => Carbon::parse($d)->format('d/m')),
            'values' => $data->pluck('total')
        ];
    }

    /**
     * Top sản phẩm bán chạy
     */
    public function getTopSellingProducts($limit = 5)
    {
        return DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')

            // ✅ FIX: join ảnh đúng cách (lấy 1 ảnh đại diện)
            ->leftJoin('product_images', function ($join) {
                $join->on('product_images.product_id', '=', 'products.id')
                    ->whereRaw('product_images.id = (
                        SELECT MIN(id) 
                        FROM product_images 
                        WHERE product_id = products.id
                    )');
            })

            ->select(
                'products.id',
                'products.name',
                'product_images.image_url', // ✅ đúng bảng
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue')
            )

            ->whereIn('orders.status', ['paid', 'completed'])

            // ⚠️ FIX: group đủ field
            ->groupBy('products.id', 'products.name', 'product_images.image_url')

            ->orderByDesc('total_sold')
            ->limit($limit)
            ->get();
    }

    /**
     * Cảnh báo tồn kho
     */
    public function getInventoryWarnings($limit = 5)
    {
        return DB::table('inventory')
            ->join('products', 'inventory.product_id', '=', 'products.id')
            ->leftJoin('product_alerts', 'products.id', '=', 'product_alerts.product_id')

            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(inventory.quantity) as current_stock'),
                DB::raw('MAX(COALESCE(product_alerts.stock_threshold, 10)) as alert_threshold')
            )

            ->groupBy('products.id', 'products.name')

            ->havingRaw('current_stock <= alert_threshold')

            ->orderBy('current_stock', 'ASC')
            ->limit($limit)
            ->get();
    }
}
