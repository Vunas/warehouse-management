<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SizeConversionRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'rule_name',
        'max_length',
        'max_width',
        'max_height',
        'slot_cost',
        'priority_level',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}