<?php

namespace App\Services;

use App\Repositories\Interfaces\PaymentRepositoryInterface;
use App\Models\Payment;
use Exception;

class PaymentService
{
    protected $paymentRepo;

    public function __construct(PaymentRepositoryInterface $paymentRepo)
    {
        $this->paymentRepo = $paymentRepo;
    }

    public function getPaginatedPayments($perPage = 15, $search = '')
    {
        $query = Payment::with('order.user');

        if (!empty($search)) {
            $query->whereHas('order', function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%");
            })->orWhere('id', 'like', "%{$search}%");
        }

        return $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
    }

    public function updatePaymentStatus($paymentId, $status)
    {
        // Chỉ cho phép cập nhật các trạng thái hợp lệ
        if (!in_array($status, ['pending', 'paid', 'failed'])) {
            throw new Exception("Trạng thái thanh toán không hợp lệ.");
        }

        return $this->paymentRepo->update($paymentId, ['status' => $status]);
    }
}