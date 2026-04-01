<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OutboundOrder extends Model
{
    use HasFactory;

    protected $table = 'outbound_orders';

    // ĐÃ THÊM warehouse_id VÀO ĐÂY
    protected $fillable = [
        'order_id',
        'staff_id',
        'warehouse_id', // BẮT BUỘC PHẢI CÓ
        'type',
        'reason',
        'status',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OutboundItem::class, 'outbound_id');
    }
    
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
}