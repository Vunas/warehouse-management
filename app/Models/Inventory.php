<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    // Đảm bảo có batch_id ở đây
    protected $fillable = [
        'product_id',
        'location_id',
        'batch_id', 
        'quantity',
        'reserved_quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    // ĐẢM BẢO PHẢI CÓ HÀM NÀY ĐỂ TRÁNH LỖI 500 KHI AJAX GỌI
    public function batch()
    {
        return $this->belongsTo(ProductBatch::class, 'batch_id');
    }
}