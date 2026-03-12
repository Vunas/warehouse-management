<?php

namespace App\Services;

use App\Repositories\Interfaces\CartItemRepositoryInterface;

class CartService
{
    protected $cartRepo;

    public function __construct(CartItemRepositoryInterface $cartRepo)
    {
        $this->cartRepo = $cartRepo;
    }

    public function getUserCart($userId)
    {
        return $this->cartRepo->getByUserId($userId);
    }

    public function addToCart(array $data)
    {
        // Có thể thêm logic check xem sản phẩm đã có trong giỏ chưa, nếu có thì cộng dồn số lượng
        return $this->cartRepo->create($data);
    }

    public function updateCartQuantity($cartItemId, int $quantity)
    {
        return $this->cartRepo->update($cartItemId, ['quantity' => $quantity]);
    }

    public function removeCartItem($cartItemId)
    {
        return $this->cartRepo->delete($cartItemId);
    }
}