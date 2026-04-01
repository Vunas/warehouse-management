<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    protected $fillable = [
        'product_id',
        'location_id',
        'batch_id',
        'transaction_type', 
        'reference_id',
        'quantity_change',
        'balance_after',
        'staff_id',
        'note',
    ];

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

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}