<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InboundDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'inbound_id',
        'product_id',
        'input_length',
        'input_width',
        'input_height',
        'quantity',
    ];

    public function inboundTicket()
    {
        return $this->belongsTo(InboundTicket::class, 'inbound_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function calculatedSlot()
    {
        return $this->hasOne(CalculatedSlot::class, 'inbound_detail_id');
    }
}