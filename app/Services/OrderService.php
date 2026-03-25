<?php

namespace App\Services;

use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\OrderItemRepositoryInterface;
use App\Repositories\Interfaces\CartItemRepositoryInterface;
// Thay InventoryRepo thành InventoryService
use App\Services\InventoryService; 
use Illuminate\Support\Facades\DB;
use Exception;

class OrderService
{
    protected $orderRepo;
    protected $orderItemRepo;
    protected $cartRepo;
    protected $inventoryService;

    public function __construct(
        OrderRepositoryInterface $orderRepo,
        OrderItemRepositoryInterface $orderItemRepo,
        CartItemRepositoryInterface $cartRepo,
        InventoryService $inventoryService // Inject Service
    ) {
        $this->orderRepo = $orderRepo;
        $this->orderItemRepo = $orderItemRepo;
        $this->cartRepo = $cartRepo;
        $this->inventoryService = $inventoryService;
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
            
            $totalPrice = $cartItems->sum(function ($item) {
                return $item->quantity * $item->product->price;
            });

            $order = $this->orderRepo->create([
                'user_id' => $userId,
                'address_id' => $addressId,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'order_date' => now(),
            ]);

            foreach ($cartItems as $cartItem) {
                $this->orderItemRepo->create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price, // Lưu lại giá lúc mua
                ]);

                // GỌI INVENTORY SERVICE: Để nó tự lo thuật toán trừ kho FIFO
                $this->inventoryService->deductStockForOrder($cartItem->product_id, $cartItem->quantity);
            }

            // (Lưu ý: Bạn cần khai báo hàm clearCart trong CartItemRepository)
            $this->cartRepo->clearCart($userId);

            return $order;
        });
    }

    public function updateOrderStatus($id, $newStatus)
    {
        return $this->orderRepo->update($id, ['status' => $newStatus]);
    }
}