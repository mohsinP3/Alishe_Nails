<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A single post pulled from the business Instagram account (@alishe_nails)
 * by the Instagram Graph API sync (see App\Services\InstagramFeedService).
 *
 * The table doubles as the site's cache of the Instagram feed: the homepage
 * only ever reads from here, so if the API/token fails the last synced
 * posts keep displaying and the site never crashes.
 */
class InstagramPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'instagram_id',
        'type',
        'media_url',
        'thumbnail_url',
        'caption',
        'permalink',
        'posted_at',
        'is_active',
    ];

    protected $casts = [
        'posted_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function isVideo(): bool
    {
        return $this->type === 'VIDEO';
    }

    /** Image to render in the gallery: video thumbnail when available, else the media itself. */
    public function getDisplayUrlAttribute(): string
    {
        return $this->thumbnail_url ?: $this->media_url;
    }

    /** The homepage gallery: newest active posts first. */
    public function scopeFeed(Builder $query, int $limit = 8): Builder
    {
        return $query->where('is_active', true)->orderByDesc('posted_at')->limit($limit);
    }
}
