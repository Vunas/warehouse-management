<?php

namespace App\Repositories\Interfaces;

interface CartItemRepositoryInterface
{
    public function all(array $columns = ['*'], array $relations = []);
    public function findById($id, array $columns = ['*'], array $relations = []);
    public function getByUserId(int $userId);
    
    public function create(array $payload);
    public function update($id, array $payload); // Giỏ hàng thì được quyền update số lượng
    public function delete($id);
    public function clearCart(int $userId); // Hàm tiện ích xóa sạch giỏ
}