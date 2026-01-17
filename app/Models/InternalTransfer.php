<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_block_id', 'to_block_id', 'trigger_reason', 'status'
    ];

    public function fromBlock()
    {
        return $this->belongsTo(StorageBlock::class, 'from_block_id');
    }

    public function toBlock()
    {
        return $this->belongsTo(StorageBlock::class, 'to_block_id');
    }

    public function items()
    {
        return $this->hasMany(TransferItem::class, 'transfer_id');
    }
}