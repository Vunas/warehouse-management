<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductBatch extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'product_batches';

    protected $fillable = [
        'product_id',
        'batch_code',
        'expiry_date',
        'manufacture_date',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'manufacture_date' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}