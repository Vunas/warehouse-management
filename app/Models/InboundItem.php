<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InboundItem extends Model
{
    use HasFactory;

    protected $table = 'inbound_items';

    protected $fillable = [
        'inbound_id',
        'product_id',
        'quantity',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function inboundOrder(): BelongsTo
    {
        return $this->belongsTo(InboundOrder::class, 'inbound_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}