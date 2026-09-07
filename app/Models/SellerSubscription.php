<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerSubscription extends Model
{
    protected $fillable = [
        'seller_id', 'subscription_plan_id', 'price', 'duration_days', 'status',
        'payment_method', 'transaction_reference', 'payment_notes', 'starts_at',
        'expires_at', 'reviewed_at', 'reviewed_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function seller(): BelongsTo { return $this->belongsTo(Seller::class); }
    public function plan(): BelongsTo { return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id'); }
    public function reviewer(): BelongsTo { return $this->belongsTo(Admin::class, 'reviewed_by'); }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expires_at?->isFuture();
    }
}
