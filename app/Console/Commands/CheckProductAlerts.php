<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProductAlertService;

class CheckProductAlerts extends Command
{
    /**
     * Tên lệnh để chạy trong terminal: php artisan alerts:check-stock
     */
    protected $signature = 'alerts:check-stock';

    protected $description = 'Kiểm tra tồn kho theo từng kho và gửi email cảnh báo thông qua Queue';

    public function handle(ProductAlertService $alertService)
    {
        $this->info('Bắt đầu quét cảnh báo tồn kho...');
        
        // Gọi service check và push job
        $alertService->checkAndSendEmailAlerts();

        $this->info('Quét hoàn tất. Các email cảnh báo đã được đẩy vào Hàng đợi (Queue).');
        return Command::SUCCESS;
    }
}