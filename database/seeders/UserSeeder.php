<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /** A couple of test customer accounts for trying out login/My Orders. */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'demo@alishenails.com'],
            [
                'name' => 'Demo Customer',
                'phone' => '+92 300 1234567',
                'password' => Hash::make('password123'),
            ]
        );
    }
}
