<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Creates one default admin account so `php artisan migrate:seed` leaves
     * the dashboard immediately usable. Credentials come from .env
     * (ADMIN_EMAIL / ADMIN_SEED_PASSWORD) purely for this one-time seed —
     * change the password from Admin > Settings immediately after first login.
     */
    public function run(): void
    {
        Admin::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@alishenails.com')],
            [
                'name' => 'Store Admin',
                'password' => Hash::make(env('ADMIN_SEED_PASSWORD', 'ChangeMe123!')),
            ]
        );
    }
}
