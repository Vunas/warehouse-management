<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InventoryTransactionRecorded
{
    use Dispatchable, SerializesModels;

    public array $transactionData;

    public function __construct(array $transactionData)
    {
        $this->transactionData = $transactionData;
    }
}
