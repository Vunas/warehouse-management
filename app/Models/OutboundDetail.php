<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutboundDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'outbound_id',
        'product_id',
        'quantity',
    ];

    public function ticket()
    {
        return $this->belongsTo(OutboundTicket::class, 'outbound_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}