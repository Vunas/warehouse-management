<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutboundTicket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contract_id',
        'requested_date',
        'status',
    ];

    protected $casts = [
        'requested_date' => 'datetime',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function details()
    {
        return $this->hasMany(OutboundDetail::class, 'outbound_id');
    }
    
    // MorphOne cho transaction (nếu cần truy xuất ngược từ ticket)
    public function transactions()
    {
        return $this->morphMany(InventoryTransaction::class, 'reference');
    }
}