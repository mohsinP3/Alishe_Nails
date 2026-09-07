<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignPitch extends Model
{
    use HasFactory;

    public const STATUSES = ['new', 'contacted', 'closed'];

    public const STATUS_LABELS = [
        'new' => 'New',
        'contacted' => 'Contacted',
        'closed' => 'Closed',
    ];

    public const CAMPAIGN_TYPES = ['homepage_feature', 'product_page_placement', 'social_shoutout', 'bundle'];

    public const CAMPAIGN_TYPE_LABELS = [
        'homepage_feature' => 'Homepage Feature',
        'product_page_placement' => 'Product Page Placement',
        'social_shoutout' => 'Social Shout-out',
        'bundle' => 'Bundle',
    ];

    public const BUDGET_RANGES = ['under_10k', '10k_30k', '30k_75k', '75k_plus'];

    public const BUDGET_RANGE_LABELS = [
        'under_10k' => 'Under PKR 10,000',
        '10k_30k' => 'PKR 10,000 – 30,000',
        '30k_75k' => 'PKR 30,000 – 75,000',
        '75k_plus' => 'PKR 75,000+',
    ];

    protected $fillable = [
        'name',
        'handle',
        'follower_count',
        'campaign_type',
        'budget_range',
        'message',
        'portfolio_links',
        'status',
    ];

    protected $casts = [
        'follower_count' => 'integer',
    ];

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function campaignTypeLabel(): string
    {
        return self::CAMPAIGN_TYPE_LABELS[$this->campaign_type] ?? $this->campaign_type;
    }

    public function budgetRangeLabel(): string
    {
        return self::BUDGET_RANGE_LABELS[$this->budget_range] ?? $this->budget_range;
    }

    public function followerCountLabel(): string
    {
        return number_format($this->follower_count);
    }
}