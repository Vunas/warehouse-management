<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class District extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'districts';

    protected $fillable = ['city_id', 'name'];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function wards(): HasMany
    {
        return $this->hasMany(Ward::class);
    }
}