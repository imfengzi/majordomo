<?php

namespace Chaos\Majordomo\Providers;

use Chaos\Majordomo\Console\InstallCommand;
use Illuminate\Support\ServiceProvider;

class MajordomoServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->registerMigrations();

            $this->commands([
                InstallCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/../../config/majordomo.php' => config_path('majordomo.php'),
            ]);
        }

        $this->loadRoutesFrom(__DIR__ . '/../router.php');
    }


    private function registerMigrations()
    {
        $migrationsPath = __DIR__ . '/../../database/migrations/';

        $items = [
            'create_admins_table.php',
            'create_menus_table.php',
            'add_category_alias_to_permissions_table.php',
            'create_routes_and_route_permissions_table.php'
        ];

        $paths = [];
        foreach ($items as $key => $name) {
            $paths[$migrationsPath . $name] = database_path('migrations') . "/" . $this->formatTimestamp($key + 1) . '_' . $name;
        }

        $this->publishes($paths, 'migrations');
    }

    /**
     * @param $addition
     * @return false|string
     */
    private function formatTimestamp($addition)
    {
        return date('Y_m_d_His', time() + $addition);
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/majordomo.php', 'majordomo'
        );
    }
}
