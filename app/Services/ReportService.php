<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

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

        $lowStockCount = DB::table('inventory')
            ->leftJoin(
                'product_alerts',
                'inventory.product_id',
                '=',
                'product_alerts.product_id'
            )
            ->select('inventory.product_id')
            ->groupBy('inventory.product_id')
            ->havingRaw(
                'SUM(inventory.quantity) <= MAX(COALESCE(product_alerts.stock_threshold, 10))'
            )
            ->count();

        return [
            'total_revenue' => $totalRevenue,
            'total_orders'  => $totalOrders,
            'total_inbounds' => $totalInbounds,
            'low_stock'     => $lowStockCount
        ];
    }

    /**
     * Biểu đồ doanh thu (Đã fix lỗi trống data)
     */
    public function getRevenueChartData($startDate, $endDate)
    {
        // 1. Tạo sẵn mảng chứa TẤT CẢ các ngày trong khoảng thời gian, mặc định giá trị = 0
        $period = CarbonPeriod::create($startDate, $endDate);
        $chartData = [];
        foreach ($period as $date) {
            $chartData[$date->format('Y-m-d')] = 0;
        }

        // 2. Query DB lấy dữ liệu thực tế
        $data = DB::table('orders')
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as total')
            ->whereIn('status', ['paid', 'completed'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupByRaw('DATE(created_at)')
            ->get();

        // 3. Ghi đè dữ liệu thực tế vào mảng đã tạo
        foreach ($data as $item) {
            $chartData[$item->date] = (float) $item->total;
        }

        // 4. Format lại để trả về cho Chart.js
        return [
            'labels' => collect(array_keys($chartData))->map(fn($d) => Carbon::parse($d)->format('d/m'))->values(),
            'values' => collect(array_values($chartData))->values()
        ];
    }

    /**
     * Biểu đồ Nhập/Xuất kho theo thời gian (Đã fix lỗi trống data)
     */
    public function getInOutChartData($startDate, $endDate)
    {
        $period = CarbonPeriod::create($startDate, $endDate);
        $chartData = [];

        foreach ($period as $date) {
            $chartData[$date->format('Y-m-d')] = [
                'in' => 0,
                'out' => 0
            ];
        }

        $data = DB::table('inventory_transactions')
            ->selectRaw("
            DATE(created_at) as date,
            SUM(CASE WHEN transaction_type = 'inbound' THEN quantity_change ELSE 0 END) as total_in,
            SUM(CASE WHEN transaction_type = 'outbound' THEN ABS(quantity_change) ELSE 0 END) as total_out
        ")
            ->whereIn('transaction_type', ['inbound', 'outbound'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupByRaw('DATE(created_at)')
            ->get();

        foreach ($data as $item) {
            if (isset($chartData[$item->date])) {
                $chartData[$item->date]['in'] = (int) $item->total_in;
                $chartData[$item->date]['out'] = (int) $item->total_out;
            }
        }

        $labels = [];
        $inValues = [];
        $outValues = [];

        foreach ($chartData as $date => $values) {
            $labels[] = Carbon::parse($date)->format('d/m');
            $inValues[] = $values['in'];
            $outValues[] = $values['out'];
        }

        return [
            'labels' => $labels,
            'in_values' => $inValues,
            'out_values' => $outValues
        ];
    }

    public function getStaffActivity($startDate, $endDate, $limit = 10)
    {
        return DB::table('inventory_transactions')
            ->join('users', 'inventory_transactions.staff_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.username',
                DB::raw('COUNT(inventory_transactions.id) as total_transactions'),
                DB::raw("
                SUM(
                    CASE
                        WHEN inventory_transactions.transaction_type = 'inbound'
                        THEN 1
                        ELSE 0
                    END
                ) as inbound_count
            "),
                DB::raw("
                SUM(
                    CASE
                        WHEN inventory_transactions.transaction_type = 'outbound'
                        THEN 1
                        ELSE 0
                    END
                ) as outbound_count
            "),
                DB::raw("
                SUM(
                    CASE
                        WHEN inventory_transactions.transaction_type = 'transfer'
                        THEN 1
                        ELSE 0
                    END
                ) as transfer_count
            ")
            )
            ->whereBetween('inventory_transactions.created_at', [$startDate, $endDate])
            ->groupBy('users.id', 'users.username')
            ->orderByDesc('total_transactions')
            ->limit($limit)
            ->get();
    }

    /**
     * Top sản phẩm bán chạy (Giữ nguyên)
     */
    public function getTopSellingProducts($limit = 5)
    {
        return DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
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
                'product_images.image_url',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue')
            )
            ->whereIn('orders.status', ['paid', 'completed'])
            ->groupBy('products.id', 'products.name', 'product_images.image_url')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->get();
    }

    /**
     * Cảnh báo tồn kho (Giữ nguyên)
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
            ->havingRaw(
                'SUM(inventory.quantity) <= MAX(COALESCE(product_alerts.stock_threshold, 10))'
            )
            ->orderBy('current_stock', 'ASC')
            ->limit($limit)
            ->get();
    }
}
