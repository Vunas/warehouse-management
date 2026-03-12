<?php

namespace App\Repositories;

use App\Models\CartItem;
use App\Repositories\Interfaces\CartItemRepositoryInterface;
use App\Repositories\Traits\CanRead;
use App\Repositories\Traits\CanWrite;
use App\Repositories\Traits\CanDelete;

class CartItemRepository extends BaseRepository implements CartItemRepositoryInterface
{
    use CanRead, CanWrite, CanDelete;

    public function getModel()
    {
        return CartItem::class;
    }

    public function getByUserId(int $userId)
    {
        return $this->model->with('product')->where('user_id', $userId)->get();
    }

    // Nghiệp vụ đặc thù: Dọn dẹp giỏ hàng sau khi đặt đơn thành công
    public function clearCart(int $userId)
    {
        return $this->model->where('user_id', $userId)->delete();
    }
}