<?php

namespace FocalStrategy\Actions;

use Illuminate\Support\ServiceProvider;
use File;

class ActionsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('action', function ($app) {
            return new ActionManager();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if (File::exists(app_path().'/actions.php')) {
            require_once app_path().'/actions.php';
        }

        $this->loadViewsFrom(__DIR__.'/views', 'actions');
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->publishes([
            __DIR__.'/public' => public_path('vendor/focalstrategy/actions'),
        ], 'public');
    }
}
