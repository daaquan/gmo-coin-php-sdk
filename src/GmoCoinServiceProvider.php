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
            $endpoint = $config['crypto_endpoint'] ?? $config['endpoint'];
            $apiKey = $config['crypto_api_key'] ?? $config['api_key'];
            $apiSecret = $config['crypto_api_secret'] ?? $config['api_secret'];
            return new GmoCoinClient($endpoint, $apiKey, $apiSecret);
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
