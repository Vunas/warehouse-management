<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorageBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'block_code',
        'total_slots',
        'status'
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class, 'block_id');
    }

    public function contractBlocks()
    {
        return $this->hasMany(ContractBlock::class, 'block_id');
    }


    public function getUsedSlotsAttribute()
    {
        return $this->inventoryItems->sum('slot_used');
    }


    public function getAvailableSlotsAttribute()
    {
        return $this->total_slots - $this->getUsedSlotsAttribute();
    }
}
