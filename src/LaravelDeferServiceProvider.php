<?php

namespace ChrGriffin\LaravelDefer;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Container\Container;
use Illuminate\View\Engines\CompilerEngine;
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
        $this->configure();
        $this->configureCompiler();
    }

    /**
     * Publish and configure the package.
     *
     * @return void
     */
    protected function configure()
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
     * Configure the compiler.
     *
     * @return void
     */
    protected function configureCompiler()
    {
        $this->app->view->getEngineResolver()->register('blade', function () {
            $compiler = $this->app['defer.compiler'];
            return new CompilerEngine($compiler);
        });

        $this->app->view->addExtension('blade.php', 'blade');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCompiler();
        $this->registerPackage();
    }

    /**
     * Register the Blade compiler.
     *
     * @return void
     */
    protected function registerCompiler()
    {
        $this->app->singleton('defer.compiler', function (Container $app) {
            $blade = $app['defer.blade'];
            $files = $app['files'];
            $storagePath = $app->config->get('view.compiled');
            return new ImageDeferCompiler($blade, $files, $storagePath);
        });

        $this->app->alias('defer.compiler', ImageDeferCompiler::class);
    }

    /**
     * Register the main package class.
     *
     * @return void
     */
    protected function registerPackage()
    {
        $this->app->singleton('defer', function () {
            return new LaravelDefer();
        });

        $this->app->alias('defer', LaravelDefer::class);
    }

    /**
     * Services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'defer',
            'defer.compiler'
        ];
    }
}