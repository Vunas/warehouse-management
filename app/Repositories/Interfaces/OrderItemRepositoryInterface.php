<?php

namespace App\Repositories\Interfaces;

interface OrderItemRepositoryInterface
{
    public function findById($id, array $columns = ['*'], array $relations = []);
    public function getByOrderId(int $orderId);
    
    public function create(array $payload);
    public function delete($id);
    // KHÔNG CÓ update()
}