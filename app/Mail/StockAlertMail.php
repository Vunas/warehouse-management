<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StockAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $alertData;

    public function __construct(array $alertData)
    {
        $this->alertData = $alertData;
    }

    public function envelope(): Envelope
    {
        $lowStockCount = count($this->alertData['low_stock'] ?? []);
        $expiringCount = count($this->alertData['expiring'] ?? []);
        $totalAlerts = $lowStockCount + $expiringCount;

        $subject = " [BÁO CÁO WMS] Có {$totalAlerts} cảnh báo Tồn kho & Hạn sử dụng cần xử lý!";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.stock-alert',
        );
    }
}
