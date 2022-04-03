<?php

namespace Yuchanns\ElasticApmTracer;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/elasticapm.php', 'elasticapm');

        $this->app->singleton(TracerElasticApm::class, static function($app) {
            return new TracerElasticApm($app);
        });

        foreach (config('elasticapm.watchers', []) as $watcher) {
            resolve($watcher)->register();
        }
    }

    public function boot()
    {
        $this->publishes([__DIR__ . '/config/elasticapm.php', config_path('elasticapm.php')], 'config');
    }
}
