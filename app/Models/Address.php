<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $user_id
 * @property int $ward_id
 * @property string $detail
 * @property bool $is_default
 * @property-read User $user
 * @property-read Ward $ward
 */
class Address extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'addresses';

    protected $fillable = [
        'user_id',
        'ward_id',
        'detail',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }
}