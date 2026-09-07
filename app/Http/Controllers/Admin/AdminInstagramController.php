<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\InstagramFeedService;
use Illuminate\Http\Request;

class AdminInstagramController extends Controller
{
    /**
     * Manual "Sync Now" for the homepage Instagram gallery — no need to
     * wait for the scheduled six-hourly run.
     */
    public function sync(Request $request, InstagramFeedService $service)
    {
        $result = $service->sync();

        return $result['success']
            ? back()->with('success', $result['message'])
            : back()->with('error', $result['message']);
    }
}
