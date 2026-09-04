<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    /**
     * Updates ADMIN_PASSWORD directly inside .env. There is intentionally no
     * `settings` database table for this single value — writing straight to
     * .env avoids inventing DB complexity for one shared password. Swap for
     * a real `admins` table with hashed passwords if multiple admin users
     * are ever needed.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if ($validated['current_password'] !== config('services.admin.password')) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $this->setEnvValue('ADMIN_PASSWORD', $validated['new_password']);

        return back()->with('success', 'Admin password updated. Use the new password next time you log in.');
    }

    private function setEnvValue(string $key, string $value): void
    {
        $envPath = base_path('.env');

        if (! file_exists($envPath)) {
            return;
        }

        $contents = file_get_contents($envPath);
        $escaped = str_contains($value, ' ') ? '"'.$value.'"' : $value;

        if (preg_match("/^{$key}=.*/m", $contents)) {
            $contents = preg_replace("/^{$key}=.*/m", "{$key}={$escaped}", $contents);
        } else {
            $contents .= "\n{$key}={$escaped}\n";
        }

        file_put_contents($envPath, $contents);
    }
}
