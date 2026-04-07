<?php

namespace App\Providers;

use App\Models\Product;
use App\Policies\ProductPolicy;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Gate;
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
        ///PASSWORD RESET LINKJE
        ///PASSWORD RESET LINKJE
       ResetPassword::createUrlUsing(function ($user, string $token) {
    return "http://localhost:5173/reset-password?token=$token&email={$user->email}";
        ///PASSWORD RESET LINKJE
        ///PASSWORD RESET LINKJE
        ///PASSWORD RESET LINKJE
});
    }
}
