<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $full_name
 * @property string $email
 * @property ?string $phone
 * @property bool $is_active
 * @property-read HasMany $addresses
 * @property-read HasMany $orders
 * @property-read HasMany $cartItems
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles; 

    protected $fillable = [
        'username',
        'password',
        'full_name',
        'email',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}