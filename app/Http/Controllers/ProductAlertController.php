<?php

namespace App\Http\Controllers;

use App\Models\ProductAlert;
use App\Models\Product;
use App\Services\ProductAlertService;
use Illuminate\Http\Request;

class ProductAlertController extends Controller
{
    protected $alertService;

    public function __construct(ProductAlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    public function index()
    {
        // 1. Lấy danh sách đang bị báo động (để show lên Dashboard)
        $triggeredAlerts = $this->alertService->getTriggeredAlerts();

        // 2. Lấy danh sách cấu hình để quản lý CRUD
        $alerts = ProductAlert::with('product')->latest()->paginate(15);

        return view('admin.product_alerts.index', compact('triggeredAlerts', 'alerts'));
    }

    public function create()
    {
        // Chỉ lấy những sản phẩm chưa được cài đặt cảnh báo
        $configuredProductIds = ProductAlert::pluck('product_id')->toArray();
        $products = Product::whereNotIn('id', $configuredProductIds)
            ->where('is_active', true)
            ->get();

        return view('admin.product_alerts.form', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id|unique:product_alerts,product_id',
            'stock_threshold' => 'required|integer|min:0',
            'expiry_threshold_days' => 'required|integer|min:0',
        ], [
            'product_id.unique' => 'Sản phẩm này đã được cài đặt cảnh báo. Vui lòng chỉnh sửa thay vì tạo mới.'
        ]);

        ProductAlert::create([
            'product_id' => $request->product_id,
            'stock_threshold' => $request->stock_threshold,
            'expiry_threshold_days' => $request->expiry_threshold_days,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('product_alerts.index')->with('success', 'Đã thêm cấu hình cảnh báo mới.');
    }

    public function edit(ProductAlert $productAlert)
    {
        // Khi edit, ta cần load kèm Product hiện tại
        $productAlert->load('product');
        return view('admin.product_alerts.form', compact('productAlert'));
    }

    public function update(Request $request, ProductAlert $productAlert)
    {
        $request->validate([
            'stock_threshold' => 'required|integer|min:0',
            'expiry_threshold_days' => 'required|integer|min:0',
        ]);

        $productAlert->update([
            'stock_threshold' => $request->stock_threshold,
            'expiry_threshold_days' => $request->expiry_threshold_days,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('product_alerts.index')->with('success', 'Cập nhật cấu hình cảnh báo thành công.');
    }

    public function destroy(ProductAlert $productAlert)
    {
        $productAlert->delete();
        return back()->with('success', 'Đã xóa cấu hình cảnh báo.');
    }

    // Toggle nhanh trạng thái bật/tắt từ ngoài bảng
    public function toggleActive(ProductAlert $productAlert)
    {
        $productAlert->update(['is_active' => !$productAlert->is_active]);
        return back()->with('success', 'Đã thay đổi trạng thái cảnh báo.');
    }
}