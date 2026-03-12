<?php

namespace App\Repositories\Interfaces;

interface PaymentRepositoryInterface
{
    public function findById($id, array $columns = ['*'], array $relations = []);
    public function getByOrderId(int $orderId);
    
    public function create(array $payload);
    public function update($id, array $payload); // Thường dùng để cập nhật status: pending -> paid
    
    // THANH TOÁN THÌ TUYỆT ĐỐI KHÔNG CÓ HÀM DELETE (Nguyên tắc tài chính)
}