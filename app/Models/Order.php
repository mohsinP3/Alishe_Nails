<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    public const STATUSES = ['pending', 'confirmed', 'processing', 'shipped', 'out_for_delivery', 'delivered', 'cancelled'];

    public const PAYMENT_STATUSES = ['pending', 'paid', 'failed', 'cancelled'];

    protected $fillable = [
        'user_id',
        'order_number',
        'access_token',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'city',
        'area',
        'postal_code',
        'payment_method',
        'transaction_reference',
        'subtotal',
        'shipping',
        'total',
        'status',
        'payment_status',
        'stock_restored_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping' => 'decimal:2',
        'total' => 'decimal:2',
        'stock_restored_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->order_number ??= 'ALN-'.strtoupper(Str::random(8));
            $order->status ??= 'pending';
            $order->payment_status ??= 'pending';
            // A random, unguessable token lets guest checkouts view their own
            // confirmation page without exposing sequential order IDs.
            $order->access_token ??= Str::random(48);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
