<?php

namespace App\Listeners;

use App\Events\OrderProcessedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendOrderNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(OrderProcessedEvent $event)
    {
        $order = $event->order;
        $reason = $event->message;
        $customer = $order->user;

        if ($customer && $customer->email) {
            try {
                // Sử dụng template HTML thay vì Mail::raw
                Mail::send('emails.order_notification', [
                    'customer' => $customer,
                    'order' => $order,
                    'reason' => $reason
                ], function ($mail) use ($customer, $order) {
                    $mail->to($customer->email)
                         ->subject("[Thông báo] Cập nhật quan trọng về Đơn hàng #ORD-{$order->id}");
                });
            } catch (\Exception $e) {
                Log::error("Lỗi gửi email cho KH: " . $e->getMessage());
            }
        }
    }
}