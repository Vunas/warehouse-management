<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'transaction_type',
        'quantity',
        'reference_id',
        'reference_type',
    ];

    // Polymorphic Relationship
    public function reference()
    {
        return $this->morphTo();
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }
}