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
        'seller_id',
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
        'media',
        'badge',
        'is_best_seller',
        'is_featured',
        'stock',
        'is_active',
        'whats_included',
    ];

    protected $casts = [
        'gallery' => 'array',
        'media' => 'array',
        'whats_included' => 'array',
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'is_best_seller' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public const LOW_STOCK_THRESHOLD = 5;

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /** Only approved reviews — use this on any public-facing page. */
    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function wishlistedBy()
    {
        return $this->belongsToMany(User::class, 'wishlists')->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where(function ($query) {
            $query->whereNull('seller_id')
                ->orWhereHas('seller', fn ($seller) => $seller->where('status', 'approved')->whereHas('subscriptions', function ($subscription) {
                    $subscription->where('status', 'active')->where('expires_at', '>', now());
                }));
        });
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    public function isLowStock(): bool
    {
        return $this->stock > 0 && $this->stock <= self::LOW_STOCK_THRESHOLD;
    }

    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }

    /**
     * Average rating rounded to 1 decimal. Falls back to 0 when no reviews exist,
     * so views must guard against an empty star display.
     */
    public function getAverageRatingAttribute(): float
    {
        return round($this->approvedReviews()->avg('rating') ?? 0, 1);
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->approvedReviews()->count();
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

    /* ------------------------------------------------------------------
     |  Unified ordered media gallery ({ type: image|video, path, order })
     * ------------------------------------------------------------------ */

    /**
     * Ordered media items, always non-empty-safe. Falls back to the legacy
     * `image` + `gallery` columns for products saved before the `media`
     * column existed, so historical rows keep rendering unchanged.
     */
    public function getMediaItemsAttribute(): array
    {
        $items = collect($this->media ?? [])
            ->filter(fn ($item) => is_array($item) && ! empty($item['type']) && ! empty($item['path']))
            ->sortBy('order')
            ->values();

        if ($items->isNotEmpty()) {
            return $items->all();
        }

        $legacy = [];

        if (! empty($this->attributes['image'])) {
            $legacy[] = ['type' => 'image', 'path' => $this->attributes['image'], 'order' => 0];
        }

        foreach ((array) ($this->gallery ?? []) as $path) {
            $legacy[] = ['type' => 'image', 'path' => $path, 'order' => count($legacy)];
        }

        return $legacy;
    }

    public function mediaUrl(string $type, string $path): string
    {
        $folder = $type === 'video' ? 'videos/products' : 'images/products';

        return asset($folder.'/'.$path);
    }

    public function mediaExists(string $type, string $path): bool
    {
        $folder = $type === 'video' ? 'videos/products' : 'images/products';

        return file_exists(public_path($folder.'/'.$path));
    }

    /**
     * Render-ready media list: only items whose file actually exists, as
     * ['type', 'url']. Views render one slot per entry — a product with a
     * single image gets exactly one slot and never an empty placeholder.
     */
    public function getMediaUrlsAttribute(): array
    {
        return collect($this->media_items)
            ->filter(fn (array $item) => $this->mediaExists($item['type'], $item['path']))
            ->map(fn (array $item) => [
                'type' => $item['type'],
                'url' => $this->mediaUrl($item['type'], $item['path']),
            ])
            ->values()
            ->all();
    }

    /** First media item in the gallery (image or video). */
    public function getCoverMediaAttribute(): ?array
    {
        return $this->media_items[0] ?? null;
    }

    /**
     * URL of the first IMAGE in the gallery — used for small thumbnails
     * (cart rows, admin lists) and OG tags where a video won't do.
     */
    public function getCoverImageUrlAttribute(): ?string
    {
        foreach ($this->media_items as $item) {
            if ($item['type'] === 'image' && $this->mediaExists($item['type'], $item['path'])) {
                return $this->mediaUrl($item['type'], $item['path']);
            }
        }

        return null;
    }

    /** Filename of the first IMAGE in the gallery (stored in cart rows). */
    public function getCoverImagePathAttribute(): ?string
    {
        foreach ($this->media_items as $item) {
            if ($item['type'] === 'image' && $this->mediaExists($item['type'], $item['path'])) {
                return $item['path'];
            }
        }

        return null;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
