<?php

namespace ChrGriffin\LaravelDefer;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Container\Container;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\Foundation\Application as LaravelApplication;
use Laravel\Lumen\Application as LumenApplication;
use Illuminate\Support\Facades\Blade;

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
        $this->bladeDirectives();
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
        
        LaravelDefer::setFunctionName(config('defer.function_name'));
        LaravelDefer::setWithScriptTags(config('defer.with_script_tags'));
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
     * Add the custom blade directives.
     *
     * @return void
     */
    protected function bladeDirectives()
    {
        Blade::directive('deferJS', function ($expression) {
            return "<?php echo ChrGriffin\\LaravelDefer\\LaravelDefer::js(); ?>";
        });
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
            $files = $app['files'];
            $storagePath = $app->config->get('view.compiled');
            return new Compilers\ImageDeferCompiler(
                $files,
                $storagePath,
                config('defer.ignored_paths'),
                config('defer.ignored_images')
            );
        });

        $this->app->alias('defer.compiler', Compilers\ImageDeferCompiler::class);
    }

    /**
     * Register the core package class.
     *
     * @return void
     */
    protected function registerPackage()
    {
        $this->app->singleton('defer', function (Container $app) {
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