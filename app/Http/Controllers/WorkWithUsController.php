<?php

namespace App\Http\Controllers;

use App\Http\Requests\CampaignPitchRequest;
use App\Models\CampaignPitch;
use Illuminate\Support\Facades\Log;

class WorkWithUsController extends Controller
{
    public function index()
    {
        return view('work-with-us.index', [
            'campaignTypes' => CampaignPitch::CAMPAIGN_TYPE_LABELS,
            'budgetRanges' => CampaignPitch::BUDGET_RANGE_LABELS,
        ]);
    }

    public function store(CampaignPitchRequest $request)
    {
        // 'website' is a honeypot field (hidden via CSS in the form) — real
        // visitors never fill it in, so CampaignPitchRequest already rejects
        // bots that do. Rate limiting on the route (see routes/web.php) covers
        // repeated automated submissions.
        $validated = $request->safe()->except('website');

        CampaignPitch::create($validated);

        // Always logged so nothing is silently lost even if notifications fail.
        Log::info('Campaign pitch submission', $validated);

        return back()->with('success', 'Thanks for pitching! We will review your campaign and get back with pricing and available slots.');
    }
}