<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;

class Seller extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'instagram_handle', 'phone', 'product_details', 'password', 'status', 'approved_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['approved_at' => 'datetime'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(SellerSubscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(SellerSubscription::class)->where('status', 'active')->where('expires_at', '>', now())->latestOfMany();
    }

    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function getPayoutDueAttribute(): float
    {
        return (float) $this->orderItems()->where('payout_status', 'pending')->sum('seller_earning');
    }
}
