<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminSettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index', ['admin' => Auth::guard('admin')->user()]);
    }

    /**
     * Real hashed-password change on the logged-in Admin's own account —
     * replaces the old .env-file-editing approach now that admins are
     * proper database accounts.
     */
    public function updatePassword(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        if (! Hash::check($validated['current_password'], $admin->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $admin->update(['password' => Hash::make($validated['new_password'])]);

        return back()->with('success', 'Admin password updated.');
    }
}
