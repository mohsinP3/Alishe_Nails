<?php

namespace App\Http\Requests;

use App\Models\CampaignPitch;
use Illuminate\Foundation\Http\FormRequest;

class CampaignPitchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'handle' => ['required', 'string', 'max:150'],
            'follower_count' => ['required', 'integer', 'min:0', 'max:1000000000'],
            'campaign_type' => ['required', 'string', 'in:'.implode(',', CampaignPitch::CAMPAIGN_TYPES)],
            'budget_range' => ['required', 'string', 'in:'.implode(',', CampaignPitch::BUDGET_RANGES)],
            'message' => ['required', 'string', 'max:2000'],
            'portfolio_links' => ['nullable', 'string', 'max:2000'],
            // Honeypot field — real users never fill a hidden input.
            'website' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'website.prohibited' => 'Submission rejected.',
        ];
    }

    public function attributes(): array
    {
        return [
            'handle' => 'Instagram / TikTok handle',
            'follower_count' => 'follower count',
            'portfolio_links' => 'portfolio links',
        ];
    }
}