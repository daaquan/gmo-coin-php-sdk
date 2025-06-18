<?php

namespace GmoCoin;

use Illuminate\Support\ServiceProvider;

class GmoCoinServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/gmocoin.php', 'gmocoin');

        $this->app->singleton(GmoCoinClient::class, function ($app) {
            $config = $app['config']->get('gmocoin');
            return new GmoCoinClient($config['endpoint'], $config['api_key'], $config['api_secret']);
        });
    }

    public function boot()
    {
        $path = function_exists('config_path')
            ? config_path('gmocoin.php')
            : base_path('config/gmocoin.php');

        $this->publishes([
            __DIR__.'/../config/gmocoin.php' => $path,
        ], 'config');
    }
}

