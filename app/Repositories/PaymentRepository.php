<?php

namespace App\Repositories;

use App\Models\Payment;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use App\Repositories\Traits\CanRead;
use App\Repositories\Traits\CanWrite;

class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface
{
    use CanRead;
    use CanWrite {
        create as traitCreate;
        update as traitUpdate;
    }
    // TUYỆT ĐỐI KHÔNG CÓ CanDelete

    public function getModel()
    {
        return Payment::class;
    }

    public function create(array $payload)
    {
        return $this->traitCreate($payload);
    }

    public function update($id, array $payload)
    {
        return $this->traitUpdate($id, $payload);
    }

    public function getByOrderId(int $orderId)
    {
        return $this->model->where('order_id', $orderId)->first();
    }
}