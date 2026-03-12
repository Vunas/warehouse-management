<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutboundItem extends Model
{
    use HasFactory;

    protected $table = 'outbound_items';

    protected $fillable = [
        'outbound_id',
        'product_id',
        'quantity',
    ];

    public function outboundOrder(): BelongsTo
    {
        return $this->belongsTo(OutboundOrder::class, 'outbound_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}