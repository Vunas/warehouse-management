<?php

namespace App\Services;

use App\Models\ProductAlert;
use App\Models\Inventory;
use App\Models\ProductBatch;
use App\Models\User;
use App\Jobs\SendStockAlertEmail;
use Carbon\Carbon;

class ProductAlertService
{
    /**
     * ===============================
     * 1. BUILD DATA DÙNG CHUNG
     * ===============================
     */
    private function buildAlertData(ProductAlert $alert): ?array
    {
        if (!$alert->product) return null;

        $data = [
            'low_stock' => [],
            'expiring'  => []
        ];

        // ===============================
        // LOW STOCK
        // ===============================
        $stocks = Inventory::where('inventory.product_id', $alert->product_id)
            ->join('locations', 'inventory.location_id', '=', 'locations.id')
            ->join('warehouses', 'locations.warehouse_id', '=', 'warehouses.id')
            ->selectRaw('warehouses.id as warehouse_id, warehouses.name as warehouse_name, SUM(inventory.quantity) as total_stock')
            ->groupBy('warehouses.id', 'warehouses.name')
            ->get();

        if ($stocks->isEmpty()) {
            if (0 <= $alert->stock_threshold) {
                $key = $alert->product_id . '-0';

                $data['low_stock'][$key] = [
                    'product_id'     => $alert->product->id,
                    'product_name'   => $alert->product->name,
                    'warehouse_name' => 'Toàn hệ thống (Chưa từng nhập kho)',
                    'current_stock'  => 0,
                    'threshold'      => $alert->stock_threshold,
                ];
            }
        } else {
            foreach ($stocks as $s) {
                if ($s->total_stock <= $alert->stock_threshold) {
                    $key = $alert->product_id . '-' . $s->warehouse_id;

                    $data['low_stock'][$key] = [
                        'product_id'     => $alert->product->id,
                        'product_name'   => $alert->product->name,
                        'warehouse_name' => $s->warehouse_name,
                        'current_stock'  => $s->total_stock,
                        'threshold'      => $alert->stock_threshold,
                    ];
                }
            }
        }

        // ===============================
        // EXPIRY
        // ===============================
        $limitDate = Carbon::now()->addDays($alert->expiry_threshold_days);

        $batchIds = Inventory::where('product_id', $alert->product_id)
            ->where('quantity', '>', 0)
            ->whereNotNull('batch_id')
            ->pluck('batch_id')
            ->unique();

        if ($batchIds->isNotEmpty()) {
            $batches = ProductBatch::whereIn('id', $batchIds)
                ->whereNotNull('expiry_date')
                ->where('expiry_date', '<=', $limitDate)
                ->get();

            foreach ($batches as $b) {
                $key = $alert->product_id . '-' . $b->id;

                $daysLeft = Carbon::now()
                    ->startOfDay()
                    ->diffInDays(Carbon::parse($b->expiry_date)->startOfDay(), false);

                $data['expiring'][$key] = [
                    'product_id'     => $alert->product->id,
                    'product_name'   => $alert->product->name,
                    'batch_code'     => $b->batch_code,
                    'expiry_date'    => Carbon::parse($b->expiry_date)->format('d/m/Y'),
                    'days_left'      => $daysLeft,
                    'threshold_days' => $alert->expiry_threshold_days,
                ];
            }
        }

        return $data;
    }

    /**
     * ===============================
     * 2. DASHBOARD (REALTIME)
     * ===============================
     */
    public function getTriggeredAlerts(): array
    {
        $alerts = ProductAlert::with('product')
            ->where('is_active', true)
            ->get();

        $result = [
            'low_stock'     => [],
            'expiring_soon' => []
        ];

        foreach ($alerts as $alert) {
            $data = $this->buildAlertData($alert);
            if (!$data) continue;

            $result['low_stock'] = array_merge($result['low_stock'], array_values($data['low_stock']));
            $result['expiring_soon'] = array_merge($result['expiring_soon'], array_values($data['expiring']));
        }

        // sort
        usort($result['low_stock'], fn($a, $b) => $a['current_stock'] <=> $b['current_stock']);
        usort($result['expiring_soon'], fn($a, $b) => $a['days_left'] <=> $b['days_left']);

        return $result;
    }

    /**
     * ===============================
     * 3. LẤY EMAIL NGƯỜI NHẬN
     * ===============================
     */
    private function getNotifyEmails(): array
    {
        return User::permission('receive_alert_emails')
            ->where('is_active', true)
            ->pluck('email')
            ->toArray();
    }

    /**
     * ===============================
     * 4. CHECK & SEND MAIL CRONJOB
     * ===============================
     */
    public function checkAndSendEmailAlerts(): void
    {
        $emails = $this->getNotifyEmails();

        if (empty($emails)) return;

        $alerts = ProductAlert::with('product')
            ->where('is_active', true)
            ->get();

        $now = Carbon::now();

        $mailData = [
            'low_stock' => [],
            'expiring'  => []
        ];

        $triggeredStockAlertIds = [];
        $triggeredExpiryAlertIds = [];

        foreach ($alerts as $alert) {
            if (!$alert->product) continue;

            $data = $this->buildAlertData($alert);
            if (!$data) continue;

            // ================= LOW STOCK =================
            if (!$alert->last_stock_alert_at || $alert->last_stock_alert_at->diffInHours($now) >= 24) {
                if (!empty($data['low_stock'])) {
                    $mailData['low_stock'] = array_merge($mailData['low_stock'], array_values($data['low_stock']));
                    $triggeredStockAlertIds[] = $alert->id;
                }
            }

            // ================= EXPIRY =================
            if (!$alert->last_expiry_alert_at || $alert->last_expiry_alert_at->diffInHours($now) >= 24) {
                if (!empty($data['expiring'])) {
                    $mailData['expiring'] = array_merge($mailData['expiring'], array_values($data['expiring']));
                    $triggeredExpiryAlertIds[] = $alert->id;
                }
            }
        }

        // ================= SEND 1 MAIL & UPDATE DB =================
        if (!empty($mailData['low_stock']) || !empty($mailData['expiring'])) {

            // Sort mảng trước khi gửi mail cho đẹp
            usort($mailData['low_stock'], fn($a, $b) => $a['current_stock'] <=> $b['current_stock']);
            usort($mailData['expiring'], fn($a, $b) => $a['days_left'] <=> $b['days_left']);

            // Gửi job
            SendStockAlertEmail::dispatch($mailData, $emails);

            // FIX: Tối ưu Update Database (Bulk update thay vì foreach)
            if (!empty($triggeredStockAlertIds)) {
                ProductAlert::whereIn('id', $triggeredStockAlertIds)->update(['last_stock_alert_at' => $now]);
            }

            if (!empty($triggeredExpiryAlertIds)) {
                ProductAlert::whereIn('id', $triggeredExpiryAlertIds)->update(['last_expiry_alert_at' => $now]);
            }
        }
    }
}
