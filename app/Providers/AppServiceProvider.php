<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Horizon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Horizon::routeMailNotificationsTo('bagaswisnu07@gmail.com');
        Horizon::auth(function ($request) {
            return auth()->check() && $request->user()->is_admin;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
