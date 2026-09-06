<?php

namespace App\Providers;

use App\Support\Cart;
use Illuminate\Pagination\Paginator;
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
        // Custom pagination markup that matches the site's design system
        // instead of the default Tailwind classes (the site uses plain CSS).
        Paginator::defaultView('vendor.pagination.alishe');
        Paginator::defaultSimpleView('vendor.pagination.simple-alishe');

        // Cart count is used by the navbar component on every page,
        // so it is shared globally instead of being passed from every controller.
        View::composer('*', function ($view) {
            $view->with('cartCount', Cart::count());
        });
    }
}
