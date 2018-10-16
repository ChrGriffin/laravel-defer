<?php

namespace ChrGriffin\LaravelDefer;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Application as LaravelApplication;
use Laravel\Lumen\Application as LumenApplication;

class LaravelDeferServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->setup();
    }

    /**
     * Set up the package configuration.
     *
     * @return void
     */
    protected function setup()
    {
        $path = realpath(__DIR__ . '/../config/defer.php');

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$path => config_path('defer.php')]);
        }
        elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('defer.php');
        }

        $this->mergeConfigFrom($path, 'defer');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('defer', function (Container $app) {
            return new LaravelDefer();
        });

        $this->app->alias('defer', LaravelDefer::class);
    }
}