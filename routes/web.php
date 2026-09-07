<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\AdminAnalyticsController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminCampaignPitchController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminCouponController;
use App\Http\Controllers\Admin\AdminCustomerController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminInstagramController;
use App\Http\Controllers\Admin\AdminMarketplaceController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminSellerController;
use App\Http\Controllers\Admin\AdminSubscriptionController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\AdminShippingController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomDesignController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HowToApplyController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SellerAuthController;
use App\Http\Controllers\SellerDashboardController;
use App\Http\Controllers\SellerSubscriptionController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TrackOrderController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\WorkWithUsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Alishe Nails - Public Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');

Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::post('/products/{product:slug}/reviews', [ReviewController::class, 'store'])
    ->middleware(['auth:web', 'throttle:5,1'])
    ->name('reviews.store');
Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])
    ->middleware('auth:web')
    ->name('reviews.destroy');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/update/{rowId}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{rowId}', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::get('/checkout/shipping-fee', [CheckoutController::class, 'calculateShippingFee'])->name('checkout.shippingFee');
Route::post('/checkout/coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.coupon.apply');
Route::delete('/checkout/coupon', [CheckoutController::class, 'removeCoupon'])->name('checkout.coupon.remove');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

Route::get('/track-order', [TrackOrderController::class, 'index'])->name('track-order.index');
Route::post('/track-order', [TrackOrderController::class, 'search'])->name('track-order.search');

Route::get('/about', [AboutController::class, 'index'])->name('about.index');
Route::get('/how-to-apply', [HowToApplyController::class, 'index'])->name('how-to-apply.index');
Route::get('/policies', [PolicyController::class, 'index'])->name('policies.index');

Route::get('/work-with-us', [WorkWithUsController::class, 'index'])->name('work-with-us.index');
Route::post('/work-with-us', [WorkWithUsController::class, 'store'])->middleware('throttle:5,1')->name('work-with-us.store');

Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');
Route::get('/custom-design', [CustomDesignController::class, 'create'])->name('custom-design.create');
Route::post('/custom-design', [CustomDesignController::class, 'store'])->middleware('throttle:5,1')->name('custom-design.store');

Route::post('/newsletter/subscribe', [NewsletterController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

Route::get('/sell-with-us', [SellerAuthController::class, 'apply'])->name('seller.apply');
Route::post('/sell-with-us', [SellerAuthController::class, 'storeApplication'])->middleware('throttle:5,1')->name('seller.apply.store');
Route::get('/seller/login', [SellerAuthController::class, 'login'])->name('seller.login');
Route::post('/seller/login', [SellerAuthController::class, 'authenticate'])->middleware('throttle:5,1')->name('seller.login.attempt');
Route::post('/seller/logout', [SellerAuthController::class, 'logout'])->middleware('seller')->name('seller.logout');

Route::middleware('seller')->prefix('seller')->name('seller.')->group(function () {
    Route::get('/', [SellerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/subscription', [SellerSubscriptionController::class, 'create'])->name('subscription');
    Route::post('/subscription', [SellerSubscriptionController::class, 'store'])->name('subscription.store');
    Route::middleware('seller.subscription')->group(function () {
        Route::get('/products/create', [SellerDashboardController::class, 'createProduct'])->name('products.create');
        Route::post('/products', [SellerDashboardController::class, 'storeProduct'])->name('products.store');
        Route::get('/products/{product}/edit', [SellerDashboardController::class, 'editProduct'])->name('products.edit');
        Route::put('/products/{product}', [SellerDashboardController::class, 'updateProduct'])->name('products.update');
        Route::delete('/products/{product}', [SellerDashboardController::class, 'destroyProduct'])->name('products.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Customer authentication ('web' guard)
|--------------------------------------------------------------------------
*/
Route::middleware('guest:web')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:5,1');

    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])
        ->middleware('throttle:3,1')
        ->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('password.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth:web')->name('logout');

Route::middleware('auth:web')->prefix('account')->name('account.')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
    Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    Route::get('/orders', [CustomerOrderController::class, 'index'])->name('orders');
    Route::get('/orders/{order}', [CustomerOrderController::class, 'show'])->name('orders.show');
});

/*
|--------------------------------------------------------------------------
| Admin ('admin' guard — completely separate from customer auth)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->middleware('throttle:5,1')->name('login.attempt');
    });

    Route::post('/logout', [AdminAuthController::class, 'logout'])->middleware('admin')->name('logout');

    Route::middleware('admin')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('products', AdminProductController::class)->names('products')->except(['show']);
        Route::resource('categories', AdminCategoryController::class)->names('categories')->except(['show']);
        Route::resource('shipping', AdminShippingController::class)->names('shipping')->except(['show']);

        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::patch('/orders/{order}/payment-status', [AdminOrderController::class, 'updatePaymentStatus'])->name('orders.updatePaymentStatus');

        Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/{customer}', [AdminCustomerController::class, 'show'])->name('customers.show');

        Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::patch('/reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
        Route::patch('/reviews/{review}/reject', [AdminReviewController::class, 'reject'])->name('reviews.reject');
        Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

        // Creator & influencer campaign pitches submitted via /work-with-us.
        Route::get('/campaign-pitches', [AdminCampaignPitchController::class, 'index'])->name('campaign-pitches.index');
        Route::patch('/campaign-pitches/{pitch}/status', [AdminCampaignPitchController::class, 'updateStatus'])->name('campaign-pitches.status');
        Route::delete('/campaign-pitches/{pitch}', [AdminCampaignPitchController::class, 'destroy'])->name('campaign-pitches.destroy');

        Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics.index');

        Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings/password', [AdminSettingsController::class, 'updatePassword'])->name('settings.password');

        // Manual "Sync Now" for the homepage Instagram gallery.
        Route::post('/instagram/sync', [AdminInstagramController::class, 'sync'])->name('instagram.sync');

        Route::get('/marketplace', [AdminMarketplaceController::class, 'index'])->name('marketplace.index');
        Route::post('/marketplace/settings', [AdminMarketplaceController::class, 'updateSettings'])->name('marketplace.settings');
        Route::post('/marketplace/sellers/{seller}/approve', [AdminMarketplaceController::class, 'approve'])->name('marketplace.sellers.approve');
        Route::post('/marketplace/sellers/{seller}/reject', [AdminMarketplaceController::class, 'reject'])->name('marketplace.sellers.reject');
        Route::post('/marketplace/sellers/{seller}/mark-paid', [AdminMarketplaceController::class, 'markPaid'])->name('marketplace.sellers.markPaid');
        Route::get('/marketplace/sellers', [AdminSellerController::class, 'index'])->name('marketplace.sellers.index');
        Route::get('/marketplace/sellers/{seller}', [AdminSellerController::class, 'show'])->name('marketplace.sellers.show');

        Route::get('/subscriptions', [AdminSubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::post('/subscriptions/plans', [AdminSubscriptionController::class, 'storePlan'])->name('subscriptions.plans.store');
        Route::patch('/subscriptions/plans/{plan}', [AdminSubscriptionController::class, 'updatePlan'])->name('subscriptions.plans.update');
        Route::post('/subscriptions/{subscription}/activate', [AdminSubscriptionController::class, 'activate'])->name('subscriptions.activate');
        Route::post('/subscriptions/{subscription}/reject', [AdminSubscriptionController::class, 'reject'])->name('subscriptions.reject');

        Route::get('/coupons', [AdminCouponController::class, 'index'])->name('coupons.index');
        Route::post('/coupons', [AdminCouponController::class, 'store'])->name('coupons.store');
        Route::delete('/coupons/{coupon}', [AdminCouponController::class, 'destroy'])->name('coupons.destroy');
    });
});
