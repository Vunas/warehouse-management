<?php

namespace App\Services;

use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\OrderItemRepositoryInterface;
use App\Repositories\Interfaces\CartItemRepositoryInterface;
use App\Repositories\Interfaces\InventoryRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderService
{
    protected $orderRepo;
    protected $orderItemRepo;
    protected $cartRepo;
    protected $inventoryRepo;

    public function __construct(
        OrderRepositoryInterface $orderRepo,
        OrderItemRepositoryInterface $orderItemRepo,
        CartItemRepositoryInterface $cartRepo,
        InventoryRepositoryInterface $inventoryRepo
    ) {
        $this->orderRepo = $orderRepo;
        $this->orderItemRepo = $orderItemRepo;
        $this->cartRepo = $cartRepo;
        $this->inventoryRepo = $inventoryRepo;
    }

    public function getPaginatedOrders($perPage = 15)
    {
        return $this->orderRepo->paginate($perPage, ['*'], ['user', 'address']);
    }

    public function getOrderById($id)
    {
        return $this->orderRepo->findById($id, ['*'], ['items.product', 'payment']);
    }

    /**
     * NGHIỆP VỤ LÕI: Đặt hàng từ Giỏ hàng
     */
    public function placeOrder(int $userId, int $addressId)
    {
        $cartItems = $this->cartRepo->getByUserId($userId);

        if ($cartItems->isEmpty()) {
            throw new Exception("Giỏ hàng đang trống.");
        }

        return DB::transaction(function () use ($userId, $addressId, $cartItems) {
            
            // 1. Tính tổng tiền
            $totalPrice = $cartItems->sum(function ($item) {
                return $item->quantity * $item->product->price;
            });

            // 2. Tạo đơn hàng
            $order = $this->orderRepo->create([
                'user_id' => $userId,
                'address_id' => $addressId,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'order_date' => now(),
            ]);

            // 3. Chuyển CartItem thành OrderItem
            foreach ($cartItems as $cartItem) {
                $this->orderItemRepo->create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price,
                ]);

                // 4. Logic trừ tồn kho (Đơn giản hóa)
                $inventory = $this->inventoryRepo->getByProductId($cartItem->product_id)->first();
                if (!$inventory || $inventory->quantity < $cartItem->quantity) {
                    throw new Exception("Sản phẩm {$cartItem->product->name} không đủ số lượng.");
                }
                $this->inventoryRepo->update($inventory->id, [
                    'quantity' => $inventory->quantity - $cartItem->quantity
                ]);
            }

            // 5. Xóa giỏ hàng
            $this->cartRepo->clearCart($userId);

            return $order;
        });
    }

    /**
     * NGHIỆP VỤ LÕI: Cập nhật trạng thái đơn
     */
    public function updateOrderStatus($id, $newStatus)
    {
        // Có thể thêm logic kiểm tra: Không được chuyển từ 'completed' về 'pending'
        return $this->orderRepo->update($id, ['status' => $newStatus]);
    }
}