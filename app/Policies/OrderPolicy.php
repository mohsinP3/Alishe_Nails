<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * A customer may only view an order that is actually theirs.
     * Guest orders (user_id null) are never viewable through this policy —
     * they're accessed via the signed access-token URL instead
     * (see CheckoutController::success).
     */
    public function view(User $user, Order $order): bool
    {
        return $order->user_id !== null && $order->user_id === $user->id;
    }
}
