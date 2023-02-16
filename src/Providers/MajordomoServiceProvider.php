<?php

namespace Chaos\Majordomo\Providers;

use Illuminate\Support\ServiceProvider;

class MajordomoServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/majordomo.php' => config_path('majordomo.php'),
        ]);
        $this->loadRoutesFrom(__DIR__ . '/../router.php');
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/majordomo.php', 'majordomo'
        );
    }
}
