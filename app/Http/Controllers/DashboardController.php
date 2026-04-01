<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $stats = $this->dashboardService->getSummaryStats();
        $lowStocks = $this->dashboardService->getLowStockAlerts(15); // Cảnh báo tồn kho dưới 15
        $recentTasks = $this->dashboardService->getRecentPendingTasks();

        return view('admin.dashboard.index', compact('stats', 'lowStocks', 'recentTasks'));
    }

    public function customerIndex(Request $request)
    {
        $search = $request->get('search');
        $filters = [
            'category_id' => $request->get('category_id'),
            'brand_id' => $request->get('brand_id'),
            'price_from' => $request->get('price_from'),
            'price_to' => $request->get('price_to'),
            'stock_status' => $request->get('stock_status'),
        ];

        $products = $this->dashboardService->getCustomerProducts($search, $filters);
        $categories = $this->dashboardService->getCategories();
        $brands = $this->dashboardService->getBrands();
        $cartStats = $this->dashboardService->getCustomerCartStats();
        $recentOrders = $this->dashboardService->getCustomerRecentOrders();

        return view('customer.dashboard.index', compact('products', 'categories', 'brands', 'cartStats', 'recentOrders'));
    }
    public function overview()
    {
        return view('customer.overview.index');
    }
}