<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type_id',
        'name',
        'total_blocks',
        'total_slots',
        'status',
        'paired_warehouse_id'
    ];

    public function type()
    {
        return $this->belongsTo(WarehouseType::class, 'type_id');
    }


    public function blocks()
    {
        return $this->hasMany(StorageBlock::class);
    }


    public function inventoryItems()
    {
        return $this->hasManyThrough(InventoryItem::class, StorageBlock::class, 'warehouse_id', 'block_id');
    }
}
