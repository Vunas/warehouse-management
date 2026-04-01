<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Hiển thị trang Dashboard/Report
     */
    public function index(Request $request)
    {
        Gate::authorize('view_reports');
        // Mặc định lấy thống kê 30 ngày gần nhất
        $days = $request->input('days', 30);
        $endDate = Carbon::now()->endOfDay();
        $startDate = Carbon::now()->subDays($days)->startOfDay();

        // Gọi Service lấy dữ liệu
        $kpis = $this->reportService->getKpiSummary($startDate, $endDate);
        $chartData = $this->reportService->getRevenueChartData($startDate, $endDate);
        $topProducts = $this->reportService->getTopSellingProducts(5);
        $warnings = $this->reportService->getInventoryWarnings(5);

        return view('admin.reports.index', compact(
            'kpis',
            'chartData',
            'topProducts',
            'warnings',
            'days'
        ));
    }
}
