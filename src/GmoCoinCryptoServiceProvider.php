<?php

namespace GmoCoin;

use Illuminate\Support\ServiceProvider;

/**
 * Laravel service provider for the crypto client.
 * Registers the {@see GmoCoinCryptoClient} and publishes the configuration file.
 */
class GmoCoinCryptoServiceProvider extends ServiceProvider
{
    /**
     * Register bindings with the application container.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/gmocoin.php', 'gmocoin');

        $this->app->singleton(GmoCoinCryptoClient::class, function (\Illuminate\Contracts\Foundation\Application $app): GmoCoinCryptoClient {
            /** @var \Illuminate\Contracts\Config\Repository $configRepo */
            $configRepo = $app->make('config');
            /** @var mixed $cfg */
            $cfg = $configRepo->get('gmocoin');
            $config = is_array($cfg) ? $cfg : [];
            $endpoint = is_string($config['crypto_endpoint'] ?? null)
                ? $config['crypto_endpoint']
                : (string) ($config['endpoint'] ?? '');
            $apiKey = is_string($config['crypto_api_key'] ?? null)
                ? $config['crypto_api_key']
                : (string) ($config['api_key'] ?? '');
            $apiSecret = is_string($config['crypto_api_secret'] ?? null)
                ? $config['crypto_api_secret']
                : (string) ($config['api_secret'] ?? '');
            return new GmoCoinCryptoClient($endpoint, $apiKey, $apiSecret);
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
