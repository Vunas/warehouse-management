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
     * BUILD CORE ALERT DATA (REUSABLE)
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
        // 1. LOW STOCK
        // ===============================
        $stocks = Inventory::where('inventory.product_id', $alert->product_id)
            ->join('locations', 'inventory.location_id', '=', 'locations.id')
            ->join('warehouses', 'locations.warehouse_id', '=', 'warehouses.id')
            ->selectRaw('warehouses.id as warehouse_id, warehouses.name as warehouse_name, SUM(inventory.quantity) as total_stock')
            ->groupBy('warehouses.id', 'warehouses.name')
            ->get();

        if ($stocks->isEmpty()) {
            if (0 <= $alert->stock_threshold) {
                $data['low_stock'][] = (object)[
                    'product'        => $alert->product,
                    'warehouse_name' => 'Toàn hệ thống (Chưa từng nhập kho)',
                    'current_stock'  => 0,
                    'threshold'      => $alert->stock_threshold,
                    'is_out_of_stock'=> true,
                ];
            }
        } else {
            foreach ($stocks as $s) {
                if ($s->total_stock <= $alert->stock_threshold) {
                    $data['low_stock'][] = (object)[
                        'product'        => $alert->product,
                        'warehouse_name' => $s->warehouse_name,
                        'current_stock'  => $s->total_stock,
                        'threshold'      => $alert->stock_threshold,
                        'is_out_of_stock'=> $s->total_stock <= 0,
                    ];
                }
            }
        }

        // ===============================
        // 2. EXPIRING
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
                $daysLeft = Carbon::now()
                    ->startOfDay()
                    ->diffInDays(Carbon::parse($b->expiry_date)->startOfDay(), false);

                $data['expiring'][] = (object)[
                    'product'   => $alert->product,
                    'batch'     => $b,
                    'days_left' => $daysLeft,
                ];
            }
        }

        return $data;
    }

    /**
     * ===============================
     * DASHBOARD REALTIME
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

            $result['low_stock'] = array_merge($result['low_stock'], $data['low_stock']);
            $result['expiring_soon'] = array_merge($result['expiring_soon'], $data['expiring']);
        }

        // Sort
        usort($result['low_stock'], fn($a, $b) => $a->current_stock <=> $b->current_stock);
        usort($result['expiring_soon'], fn($a, $b) => $a->days_left <=> $b->days_left);

        return $result;
    }

    /**
     * ===============================
     * GET EMAIL RECEIVERS
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
     * CRONJOB SEND MAIL
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

        $triggeredStockIds = [];
        $triggeredExpiryIds = [];

        foreach ($alerts as $alert) {
            $data = $this->buildAlertData($alert);
            if (!$data) continue;

            // LOW STOCK
            if (
                !$alert->last_stock_alert_at ||
                $alert->last_stock_alert_at->diffInHours($now) >= 24
            ) {
                if (!empty($data['low_stock'])) {
                    $mailData['low_stock'] = array_merge(
                        $mailData['low_stock'],
                        array_map(fn($a) => (array)$a, $data['low_stock'])
                    );
                    $triggeredStockIds[] = $alert->id;
                }
            }

            // EXPIRING
            if (
                !$alert->last_expiry_alert_at ||
                $alert->last_expiry_alert_at->diffInHours($now) >= 24
            ) {
                if (!empty($data['expiring'])) {
                    $mailData['expiring'] = array_merge(
                        $mailData['expiring'],
                        array_map(fn($a) => [
                            'product_name' => $a->product->name,
                            'batch_code'  => $a->batch->batch_code,
                            'days_left'   => $a->days_left,
                            'expiry_date' => Carbon::parse($a->batch->expiry_date)->format('d/m/Y'),
                        ], $data['expiring'])
                    );
                    $triggeredExpiryIds[] = $alert->id;
                }
            }
        }

        if (!empty($mailData['low_stock']) || !empty($mailData['expiring'])) {

            usort($mailData['low_stock'], fn($a, $b) => $a['current_stock'] <=> $b['current_stock']);
            usort($mailData['expiring'], fn($a, $b) => $a['days_left'] <=> $b['days_left']);

            SendStockAlertEmail::dispatch($mailData, $emails);

            if (!empty($triggeredStockIds)) {
                ProductAlert::whereIn('id', $triggeredStockIds)
                    ->update(['last_stock_alert_at' => $now]);
            }

            if (!empty($triggeredExpiryIds)) {
                ProductAlert::whereIn('id', $triggeredExpiryIds)
                    ->update(['last_expiry_alert_at' => $now]);
            }
        }
    }
}
