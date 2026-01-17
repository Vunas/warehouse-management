<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'block_id', 
        'product_id',
        'calc_id',
        'slot_used',
        'imported_at',
        'current_quantity',
    ];

    protected $casts = [
        'imported_at' => 'datetime',
    ];

    public function storageBlock()
    {
        return $this->belongsTo(StorageBlock::class, 'block_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class, 'item_id');
    }
}