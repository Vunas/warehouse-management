<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalculatedSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'inbound_detail_id',
        'rule_id',
        'final_length',
        'final_width',
        'final_height',
        'final_slot_cost',
        'is_violation',
    ];

    protected $casts = [
        'is_violation' => 'boolean',
    ];

    public function inboundDetail()
    {
        return $this->belongsTo(InboundDetail::class);
    }

    public function sizeRule()
    {
        return $this->belongsTo(SizeConversionRule::class, 'rule_id');
    }
}