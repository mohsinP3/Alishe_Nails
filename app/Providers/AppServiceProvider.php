<?php

namespace App\Providers;

use App\Support\Cart;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Cart count is used by the navbar component on every page,
        // so it is shared globally instead of being passed from every controller.
        View::composer('*', function ($view) {
            $view->with('cartCount', Cart::count());
        });
    }
}
