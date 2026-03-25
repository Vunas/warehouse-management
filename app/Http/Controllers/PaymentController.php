<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Exception;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
        $this->authorizeResource(Payment::class, 'payment');
    }

    public function index(Request $request)
    {
        $payments = $this->paymentService->getPaginatedPayments(
            $request->get('per_page', 15), 
            $request->get('search')
        );
        return view('admin.payments.index', compact('payments'));
    }

    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,failed'
        ]);

        try {
            $this->paymentService->updatePaymentStatus($payment->id, $request->status);
            return back()->with('success', 'Đã cập nhật trạng thái thanh toán thành công!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    
    // Override hàm destroy để chặn việc gọi qua Route
    public function destroy(Payment $payment)
    {
        abort(403, 'Hệ thống tuyệt đối không cho phép xóa bản ghi thanh toán.');
    }
}