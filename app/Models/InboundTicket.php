<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InboundTicket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contract_id',
        'expected_date',
        'status',
    ];

    protected $casts = [
        'expected_date' => 'datetime',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function details()
    {
        return $this->hasMany(InboundDetail::class, 'inbound_id');
    }

    public function hasPermission($permissionCode)
    {
        return $this->roles()->whereHas('permissions', function ($q) use ($permissionCode) {
            $q->where('code', $permissionCode);
        })->exists();
    }
}
