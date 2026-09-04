<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'price',
        'compare_at_price',
        'short_description',
        'description',
        'shape',
        'length',
        'finish',
        'image',
        'gallery',
        'badge',
        'is_best_seller',
        'is_featured',
        'stock',
        'whats_included',
    ];

    protected $casts = [
        'gallery' => 'array',
        'whats_included' => 'array',
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'is_best_seller' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Average rating rounded to 1 decimal. Falls back to 0 when no reviews exist,
     * so views must guard against an empty star display.
     */
    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    /**
     * Absolute public URL to the primary product image.
     * Returns null (not a fake path) when no image has been set,
     * so the Blade layer can render a clearly-labelled placeholder instead.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        return file_exists(public_path('images/products/'.$this->image))
            ? asset('images/products/'.$this->image)
            : null;
    }

    public function getGalleryUrlsAttribute(): array
    {
        return collect($this->gallery ?? [])
            ->filter(fn ($file) => file_exists(public_path('images/products/'.$file)))
            ->map(fn ($file) => asset('images/products/'.$file))
            ->values()
            ->all();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
