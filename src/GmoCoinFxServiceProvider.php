<?php

namespace GmoCoin;

use Illuminate\Support\ServiceProvider;

/**
 * Laravel service provider for the FX client.
 * Registers the {@see GmoCoinFxClient} in the container and publishes
 * the configuration file.
 */
class GmoCoinFxServiceProvider extends ServiceProvider
{
    /**
     * Register bindings with the application container.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/gmocoin.php', 'gmocoin');

        $this->app->singleton(GmoCoinFxClient::class, function (\Illuminate\Contracts\Foundation\Application $app): GmoCoinFxClient {
            /** @var \Illuminate\Contracts\Config\Repository $configRepo */
            $configRepo = $app->make('config');
            /** @var mixed $cfg */
            $cfg = $configRepo->get('gmocoin');
            $config = is_array($cfg) ? $cfg : [];
            $endpoint = is_string($config['fx_endpoint'] ?? null)
                ? $config['fx_endpoint']
                : (string) ($config['endpoint'] ?? '');
            $apiKey = is_string($config['fx_api_key'] ?? null)
                ? $config['fx_api_key']
                : (string) ($config['api_key'] ?? '');
            $apiSecret = is_string($config['fx_api_secret'] ?? null)
                ? $config['fx_api_secret']
                : (string) ($config['api_secret'] ?? '');
            return new GmoCoinFxClient($endpoint, $apiKey, $apiSecret);
        });
    }

    /**
     * Publish configuration when the application boots.
     */
    public function boot(): void
    {
        $path = function_exists('config_path')
            ? config_path('gmocoin.php')
            : base_path('config/gmocoin.php');

        $this->publishes([
            __DIR__.'/../config/gmocoin.php' => $path,
        ], 'config');
    }
}

