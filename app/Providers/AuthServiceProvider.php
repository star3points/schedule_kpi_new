<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        /*
        Gate::define('kpi_shops', function (BitrixUser $bitrixUser) {
            return $bitrixUser->isAdmin();
        });
        Gate::define('kpi_shop', function (BitrixUser $bitrixUser, string $shopId) {
            return $bitrixUser->isAdmin() || $bitrixUser->isManagerOf($shopId);
        });
        */
    }
}
