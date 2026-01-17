<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id', 'block_id', 'slots_committed', 'rented_from', 'rented_to', 'rental_price'
    ];

    protected $casts = [
        'rented_from' => 'datetime',
        'rented_to' => 'datetime',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function storageBlock()
    {
        return $this->belongsTo(StorageBlock::class, 'block_id');
    }
}