<?php 

namespace Deviny\Excelify;
use Deviny\Excelify\Middleware\Localization;
use Illuminate\Contracts\Http\Kernel;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/excelify.php', 'excelify'); 
    }
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadViewsFrom(__DIR__.'/views', 'excelify');
        $this->loadTranslationsFrom(__DIR__.'/lang', 'excelify');
        $this->registerMiddleware(Localization::class);

        $this->publishes([
            __DIR__.'/../config/excelify.php'=>config_path('excelify.php'),
            __DIR__.'/lang' => resource_path('lang/vendor/excelify'),
            __DIR__.'/views' => resource_path('views/vendor/excelify'),
            __DIR__.'/js' => public_path('js/excelify/js'),
            __DIR__.'/css' => public_path('css/excelify/css'),
        ]);        
    }

    protected function registerMiddleware($middleware)
    {
        $kernel = $this->app[Kernel::class];
        $kernel->pushMiddleware($middleware);
    }
}

