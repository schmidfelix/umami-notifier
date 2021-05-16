<?php

namespace App\Providers;

use App\Support\Umami\UmamiClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UmamiClient::class, fn () => UmamiClient::login(
            user: config('umami.user'),
            password: config('umami.password'),
        ));
    }
}
