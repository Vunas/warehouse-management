<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseType extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'priority_rule', 'description'];

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class, 'type_id');
    }
}