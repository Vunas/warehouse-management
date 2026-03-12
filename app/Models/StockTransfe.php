<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockTransfer extends Model
{
    use HasFactory;

    protected $table = 'stock_transfers';

    protected $fillable = [
        'from_shelf_id',
        'to_shelf_id',
        'staff_id',
        'status',
    ];

    public function fromShelf(): BelongsTo
    {
        return $this->belongsTo(Shelf::class, 'from_shelf_id');
    }

    public function toShelf(): BelongsTo
    {
        return $this->belongsTo(Shelf::class, 'to_shelf_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransferItem::class, 'transfer_id');
    }
}