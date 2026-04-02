<?php

namespace App\Services;

use App\Events\OrderProcessedEvent;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\OrderItemRepositoryInterface;
use App\Repositories\Interfaces\CartItemRepositoryInterface;
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
        InventoryService $inventoryService
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
        return $this->orderRepo->findById($id, ['*'], ['items.product', 'payment', 'user']);
    }

    /**
     * BƯỚC 1 WMS: Đặt hàng -> Chỉ Reserve Stock
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
                'status' => 'pending', // Chuẩn: mới tạo là pending
                'order_date' => now(),
            ]);

            foreach ($cartItems as $cartItem) {
                $this->orderItemRepo->create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price,
                ]);

                // CHUẨN WMS: Chỉ cộng vào reserved_quantity
                $this->inventoryService->reserveStock($cartItem->product_id, $cartItem->quantity);
            }

            $this->cartRepo->clearCart($userId);

            return $order;
        });
    }

    /**
     * TỪ CHỐI TOÀN BỘ ĐƠN HÀNG (Trả về giỏ + Nhả tồn kho)
     */
    public function rejectOrder($orderId, $reason)
    {
        return DB::transaction(function () use ($orderId, $reason) {
            $order = $this->orderRepo->findById($orderId, ['*'], ['items', 'user']);

            // Không cho phép sửa nếu đã xử lý xong hoặc hủy
            if (in_array($order->status, ['shipping', 'completed', 'cancelled'])) {
                throw new Exception("Không thể từ chối vì đơn hàng đang giao, đã hoàn thành hoặc đã bị hủy.");
            }

            foreach ($order->items as $item) {
                // 1. Trả hàng về lại Giỏ hàng của khách
                $this->cartRepo->create([
                    'user_id' => $order->user_id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                ]);

                // 2. Nhả Tồn kho đã giữ chỗ (Release Reserved Stock)
                $this->inventoryService->releaseReservedStock($item->product_id, $item->quantity);
            }

            // 3. Cập nhật trạng thái Order thành cancelled (Không xóa order để giữ lịch sử)
            $this->orderRepo->update($orderId, ['status' => 'cancelled']);

            // 4. Bắn Event gửi Email giao diện UI
            event(new OrderProcessedEvent(
                $order,
                'order_rejected',
                $reason
            ));

            return true;
        });
    }

    /**
     * Cập nhật trạng thái đơn hàng (Khóa cứng logic)
     */
    public function updateOrderStatus($id, $newStatus)
    {
        return DB::transaction(function () use ($id, $newStatus) {
            $order = $this->orderRepo->findById($id);

            // GUARD: Khóa trạng thái nếu đã Completed hoặc Cancelled
            if (in_array($order->status, ['completed', 'cancelled'])) {
                throw new Exception("Đơn hàng đã chốt ({$order->status}). Không thể thay đổi trạng thái.");
            }

            $order = $this->orderRepo->update($id, ['status' => $newStatus]);

            // Gửi Event nếu cần thiết
            // event(new OrderProcessedEvent($order, 'status_changed', "Tình trạng đơn hàng đã cập nhật thành: " . strtoupper($newStatus)));

            return $order;
        });
    }
}
