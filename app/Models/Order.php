<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'city',
        'payment_method',
        'subtotal',
        'shipping',
        'total',
        'status',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->order_number ??= 'ALN-'.strtoupper(Str::random(8));
            $order->status ??= 'pending';
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
