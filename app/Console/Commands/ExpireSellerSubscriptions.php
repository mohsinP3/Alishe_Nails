<?php

namespace App\Console\Commands;

use App\Mail\SellerSubscriptionExpiryReminder;
use App\Models\SellerSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ExpireSellerSubscriptions extends Command
{
    protected $signature = 'sellers:expire-subscriptions';
    protected $description = 'Expire seller subscriptions, hide products, and send renewal reminders';

    public function handle(): int
    {
        SellerSubscription::with('seller')->where('status', 'active')->where('expires_at', '<=', now())->each(function (SellerSubscription $subscription) {
            $subscription->update(['status' => 'expired']);
            $subscription->seller->products()->update(['is_active' => false]);
        });

        SellerSubscription::with(['seller', 'plan'])
            ->where('status', 'active')
            ->whereBetween('expires_at', [now()->addDays(3), now()->addDays(4)])
            ->each(function (SellerSubscription $subscription) {
                try {
                    Mail::to($subscription->seller->email)->send(new SellerSubscriptionExpiryReminder($subscription));
                } catch (\Throwable $exception) {
                    report($exception);
                }
            });

        return self::SUCCESS;
    }
}
