<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderProcessedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $actionType;
    public $message;

    public function __construct($order, $actionType, $message)
    {
        $this->order = $order;
        $this->actionType = $actionType;
        $this->message = $message;
    }
}