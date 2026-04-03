<?php

namespace App\Services;

use App\Models\ProductAlert;
use App\Models\Inventory;
use App\Models\Warehouse;
use App\Models\ProductBatch;
use App\Jobs\SendStockAlertEmail;
use Carbon\Carbon;

class ProductAlertService
{
    /**
     * Dùng cho Dashboard hiển thị Real-time
     */
    public function getTriggeredAlerts()
    {
        $alerts = ProductAlert::with('product')->where('is_active', true)->get();
        $triggered = ['low_stock' => [], 'expiring_soon' => []];

        foreach ($alerts as $alert) {
            // ==========================================
            // 1. LOGIC TỒN KHO: Group theo warehouse_id
            // ==========================================
            $stocksPerWarehouse = Inventory::where('product_id', $alert->product_id)
                ->join('locations', 'inventory.location_id', '=', 'locations.id')
                ->selectRaw('locations.warehouse_id, SUM(inventory.quantity) as total_stock')
                ->groupBy('locations.warehouse_id')
                ->get();

            // FIX LOGIC CỦA BẠN: Nếu mảng rỗng (Sản phẩm chưa từng nhập kho) -> Tồn kho = 0
            if ($stocksPerWarehouse->isEmpty()) {
                if (0 <= $alert->stock_threshold) {
                    $triggered['low_stock'][] = (object) [
                        'alert_id' => $alert->id,
                        'product' => $alert->product,
                        'warehouse_name' => 'Toàn hệ thống (Chưa có hàng)',
                        'current_stock' => 0,
                        'threshold' => $alert->stock_threshold,
                        'is_out_of_stock' => true
                    ];
                }
            } else {
                // Nếu đã có trong kho, duyệt qua từng kho xem kho nào dưới ngưỡng
                foreach ($stocksPerWarehouse as $stockInfo) {
                    if ($stockInfo->total_stock <= $alert->stock_threshold) {
                        $warehouse = Warehouse::find($stockInfo->warehouse_id);

                        $triggered['low_stock'][] = (object) [
                            'alert_id' => $alert->id,
                            'product' => $alert->product,
                            'warehouse_name' => $warehouse->name ?? 'N/A',
                            'current_stock' => $stockInfo->total_stock,
                            'threshold' => $alert->stock_threshold,
                            'is_out_of_stock' => $stockInfo->total_stock == 0
                        ];
                    }
                }
            }

            // ==========================================
            // 2. LOGIC HẠN SỬ DỤNG
            // ==========================================
            $expiryLimitDate = Carbon::now()->addDays($alert->expiry_threshold_days);

            // Chỉ cảnh báo HSD cho những lô hàng ĐANG CÓ tồn kho (>0)
            $activeBatchIds = Inventory::where('product_id', $alert->product_id)
                ->where('quantity', '>', 0)
                ->whereNotNull('batch_id')
                ->pluck('batch_id')
                ->unique();

            if ($activeBatchIds->isNotEmpty()) {
                $expiringBatches = ProductBatch::whereIn('id', $activeBatchIds)
                    ->whereNotNull('expiry_date')
                    ->where('expiry_date', '<=', $expiryLimitDate)
                    ->orderBy('expiry_date', 'asc')
                    ->get();

                foreach ($expiringBatches as $batch) {
                    $daysLeft = Carbon::now()->startOfDay()->diffInDays(Carbon::parse($batch->expiry_date)->startOfDay(), false);

                    $triggered['expiring_soon'][] = (object) [
                        'alert_id' => $alert->id,
                        'product' => $alert->product,
                        'batch' => $batch,
                        'days_left' => $daysLeft,
                        'threshold_days' => $alert->expiry_threshold_days
                    ];
                }
            }
        }

        usort($triggered['low_stock'], fn($a, $b) => $a->current_stock <=> $b->current_stock);
        usort($triggered['expiring_soon'], fn($a, $b) => $a->days_left <=> $b->days_left);

        return $triggered;
    }

    /**
     * Hàm này dành cho Command chạy ngầm gửi Email
     */
    public function checkAndSendEmailAlerts()
    {
        $alerts = ProductAlert::with('product')->where('is_active', true)->get();
        $now = Carbon::now();
        $adminEmail = env('ADMIN_ALERT_EMAIL', 'none.pazo@gmail.com');

        // Khởi tạo mảng chứa toàn bộ dữ liệu để gửi 1 mail duy nhất
        $mailData = [
            'low_stock' => [],
            'expiring'  => []
        ];

        // Mảng lưu vết những alert nào đã được kích hoạt để update thời gian (Chống spam ngày hôm sau)
        $triggeredStockAlerts = [];
        $triggeredExpiryAlerts = [];

        foreach ($alerts as $alert) {
            // ==========================================
            // 1. QUÉT TỒN KHO TỪNG KHO
            // ==========================================
            if (!$alert->last_stock_alert_at || $alert->last_stock_alert_at->diffInHours($now) >= 24) {
                $stocksPerWarehouse = Inventory::where('product_id', $alert->product_id)
                    ->join('locations', 'inventory.location_id', '=', 'locations.id')
                    ->selectRaw('locations.warehouse_id, SUM(inventory.quantity) as total_stock')
                    ->groupBy('locations.warehouse_id')
                    ->get();

                $hasStockTriggered = false;

                if ($stocksPerWarehouse->isEmpty()) {
                    if (0 <= $alert->stock_threshold) {
                        $hasStockTriggered = true;
                        $mailData['low_stock'][] = [
                            'product_id' => $alert->product->id,
                            'product_name' => $alert->product->name,
                            'warehouse_name' => 'Toàn hệ thống (Chưa từng nhập kho)',
                            'current_stock' => 0,
                            'threshold' => $alert->stock_threshold,
                        ];
                    }
                } else {
                    foreach ($stocksPerWarehouse as $stockInfo) {
                        if ($stockInfo->total_stock <= $alert->stock_threshold) {
                            $hasStockTriggered = true;
                            $warehouse = Warehouse::find($stockInfo->warehouse_id);

                            $mailData['low_stock'][] = [
                                'product_id' => $alert->product->id,
                                'product_name' => $alert->product->name,
                                'warehouse_name' => $warehouse->name ?? 'Unknown',
                                'current_stock' => $stockInfo->total_stock,
                                'threshold' => $alert->stock_threshold,
                            ];
                        }
                    }
                }

                if ($hasStockTriggered) {
                    $triggeredStockAlerts[] = $alert;
                }
            }

            // ==========================================
            // 2. QUÉT HẠN SỬ DỤNG
            // ==========================================
            if (!$alert->last_expiry_alert_at || $alert->last_expiry_alert_at->diffInHours($now) >= 24) {
                $expiryLimitDate = $now->copy()->addDays($alert->expiry_threshold_days);

                $activeBatchIds = Inventory::where('product_id', $alert->product_id)
                    ->where('quantity', '>', 0)
                    ->whereNotNull('batch_id')
                    ->pluck('batch_id')
                    ->unique();

                $hasExpiryTriggered = false;

                if ($activeBatchIds->isNotEmpty()) {
                    $expiringBatches = ProductBatch::whereIn('id', $activeBatchIds)
                        ->whereNotNull('expiry_date')
                        ->where('expiry_date', '<=', $expiryLimitDate)
                        ->get();

                    foreach ($expiringBatches as $batch) {
                        $hasExpiryTriggered = true;
                        $daysLeft = $now->copy()->startOfDay()->diffInDays(Carbon::parse($batch->expiry_date)->startOfDay(), false);

                        $mailData['expiring'][] = [
                            'product_id' => $alert->product->id,
                            'product_name' => $alert->product->name,
                            'batch_code' => $batch->batch_code,
                            'days_left' => $daysLeft,
                            'expiry_date' => Carbon::parse($batch->expiry_date)->format('d/m/Y'),
                            'threshold_days' => $alert->expiry_threshold_days
                        ];
                    }
                }

                if ($hasExpiryTriggered) {
                    $triggeredExpiryAlerts[] = $alert;
                }
            }
        }

        // ==========================================
        // 3. GỬI 1 EMAIL DUY NHẤT & CẬP NHẬT COOLDOWN
        // ==========================================
        if (count($mailData['low_stock']) > 0 || count($mailData['expiring']) > 0) {

            SendStockAlertEmail::dispatch($mailData, $adminEmail);

            // Chỉ cập nhật thời gian "Đã cảnh báo" cho những sản phẩm thực sự nằm trong danh sách gửi đi
            foreach ($triggeredStockAlerts as $alert) {
                $alert->update(['last_stock_alert_at' => $now]);
            }
            foreach ($triggeredExpiryAlerts as $alert) {
                $alert->update(['last_expiry_alert_at' => $now]);
            }
        }
    }
}
