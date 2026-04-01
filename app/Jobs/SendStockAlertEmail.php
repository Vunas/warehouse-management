<?php

namespace App\Jobs;

use App\Mail\StockAlertMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendStockAlertEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $alertData;
    protected $emailTo;

    public function __construct(array $alertData, string $emailTo)
    {
        $this->alertData = $alertData;
        $this->emailTo = $emailTo;
    }

    public function handle(): void
    {
        try {
            Mail::to($this->emailTo)->send(new StockAlertMail($this->alertData));
        } catch (\Exception $e) {
            Log::error('Lỗi gửi mail cảnh báo tồn kho: ' . $e->getMessage());
        }
    }
}