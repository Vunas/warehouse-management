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
        // Kiểm tra quyền (Nếu bạn có dùng Spatie Permission)
        // Gate::authorize('view_reports'); 

        // Lấy ngày bắt đầu và kết thúc từ Request, mặc định là 30 ngày qua
        $defaultStartDate = Carbon::now()->subDays(30)->format('Y-m-d');
        $defaultEndDate = Carbon::now()->format('Y-m-d');

        $startDateInput = $request->input('start_date', $defaultStartDate);
        $endDateInput = $request->input('end_date', $defaultEndDate);

        // Parse sang Carbon instance để query
        $startDate = Carbon::parse($startDateInput)->startOfDay();
        $endDate = Carbon::parse($endDateInput)->endOfDay();

        // 1. Dữ liệu cũ
        $kpis = $this->reportService->getKpiSummary($startDate, $endDate);
        $revenueChart = $this->reportService->getRevenueChartData($startDate, $endDate);
        $topProducts = $this->reportService->getTopSellingProducts(5);
        $warnings = $this->reportService->getInventoryWarnings(5);

        // 2. DỮ LIỆU MỚI: Thống kê Nhập/Xuất & Hoạt động nhân viên
        $inOutChart = $this->reportService->getInOutChartData($startDate, $endDate);
        $staffActivities = $this->reportService->getStaffActivity($startDate, $endDate);

        return view('admin.reports.index', compact(
            'kpis',
            'revenueChart',
            'topProducts',
            'warnings',
            'inOutChart',
            'staffActivities',
            'startDateInput',
            'endDateInput'
        ));
    }
}