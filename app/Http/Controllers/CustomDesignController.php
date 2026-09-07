<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomDesignRequest;
use App\Mail\CustomDesignMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CustomDesignController extends Controller
{
    public function create()
    {
        return view('custom-design.index');
    }

    public function store(CustomDesignRequest $request)
    {
        $data = $request->safe()->except(['website', 'reference_image']);
        $imagePath = $request->hasFile('reference_image')
            ? $request->file('reference_image')->store('custom-designs', 'public')
            : null;

        Log::info('Custom design request', [...$data, 'reference_image' => $imagePath]);

        try {
            Mail::to(config('services.admin.notification_email'))->send(new CustomDesignMail($data, $imagePath));
        } catch (\Throwable $e) {
            Log::warning('Custom design email failed: '.$e->getMessage());
        }

        return back()->with('success', 'Your design request is on its way. We will get back to you with a quote soon.');
    }
}
