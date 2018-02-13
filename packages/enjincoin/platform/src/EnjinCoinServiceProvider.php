<?php

namespace EnjinCoin;

use Illuminate\Support\ServiceProvider;


class EnjinCoinServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Load database migrations for new installs.
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    /**
     * Register a new alias loader instance.
     *
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/main.php', 'enjincoin'
        );
    }

}
