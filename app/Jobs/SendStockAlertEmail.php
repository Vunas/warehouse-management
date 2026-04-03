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

    protected array $alertData;

    // FIX: Cho phép truyền vào 1 mảng các email hoặc 1 chuỗi email
    protected array|string $emailTo;

    public function __construct(array $alertData, array|string $emailTo)
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
