<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CampaignPitch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminCampaignPitchController extends Controller
{
    public function index(Request $request)
    {
        $query = CampaignPitch::latest();

        $status = $request->string('status')->toString();
        if (in_array($status, CampaignPitch::STATUSES, true)) {
            $query->where('status', $status);
        }

        $pitches = $query->paginate(15)->withQueryString();

        return view('admin.campaign-pitches.index', [
            'pitches' => $pitches,
            'status' => $status,
        ]);
    }

    public function updateStatus(Request $request, CampaignPitch $pitch)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(CampaignPitch::STATUSES)],
        ]);

        $pitch->update($validated);

        return back()->with('success', 'Pitch marked as '.$pitch->statusLabel().'.');
    }

    public function destroy(CampaignPitch $pitch)
    {
        $pitch->delete();

        return back()->with('success', 'Pitch deleted.');
    }
}