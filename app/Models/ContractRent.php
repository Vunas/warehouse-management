<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractRent extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'type_id',
        'slot_quantity',
        'price_per_slot',
        'note',
    ];

    protected $casts = [
        'price_per_slot' => 'decimal:2',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function warehouseType()
    {
        return $this->belongsTo(WarehouseType::class, 'type_id');
    }
}