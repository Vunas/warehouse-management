<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTakeItem extends Model
{
    protected $fillable = [
        'stock_take_id',
        'product_id',
        'location_id',
        'batch_id',
        'expected_quantity',
        'counted_quantity',
        'variance',
        'reason',
    ];

    public function stockTake()
    {
        return $this->belongsTo(StockTake::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function batch()
    {
        return $this->belongsTo(ProductBatch::class);
    }
}