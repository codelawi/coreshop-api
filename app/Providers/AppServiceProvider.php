<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Observers\OrderObserver;
use App\Observers\ProductObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Order::observe(OrderObserver::class);
        Product::observe(ProductObserver::class);
        User::observe(UserObserver::class);

        RateLimiter::for('login', function (Request $request) {
            $key = $request->input('email').'|'.$request->ip();

            return [
                Limit::perMinute(10)->by($key),
                Limit::perHour(30)->by($key),
            ];
        });

        ResetPassword::createUrlUsing(fn ($user, string $token) => url('/api/v1/auth/reset-password').'?token='.$token.'&email='.urlencode($user->email));
    }
}
