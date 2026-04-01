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

    public function customerIndex()
    {
        $products = $this->dashboardService->getCustomerProducts();
        $cartStats = $this->dashboardService->getCustomerCartStats();

        return view('customer.dashboard.index', compact('products', 'cartStats'));
    }
    public function overview()
    {
        return view('customer.overview.index');
    }
}