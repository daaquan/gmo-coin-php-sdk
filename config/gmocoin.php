<?php

/**
 * Configuration values for the GMO Coin SDK.
 * These settings control API endpoints and credentials used by the
 * {@see GmoCoinCryptoClient} and {@see GmoCoinFxClient} classes.
 */

return [
    // Default endpoints for each API
    'crypto_endpoint' => env('GMO_COIN_CRYPTO_ENDPOINT', 'https://api.coin.z.com'),
    'fx_endpoint'     => env('GMO_COIN_FX_ENDPOINT', 'https://forex-api.coin.z.com'),

    // Legacy endpoint key kept for backwards compatibility
    'endpoint' => env('GMO_COIN_ENDPOINT', env('GMO_COIN_CRYPTO_ENDPOINT', 'https://api.coin.z.com')),

    // API credentials for each endpoint
    'crypto_api_key'    => env('GMO_COIN_CRYPTO_API_KEY', env('GMO_COIN_API_KEY', '')),
    'crypto_api_secret' => env('GMO_COIN_CRYPTO_API_SECRET', env('GMO_COIN_API_SECRET', '')),
    'fx_api_key'        => env('GMO_COIN_FX_API_KEY', env('GMO_COIN_API_KEY', '')),
    'fx_api_secret'     => env('GMO_COIN_FX_API_SECRET', env('GMO_COIN_API_SECRET', '')),

    // Legacy keys kept for backwards compatibility
    'api_key'    => env('GMO_COIN_API_KEY', ''),
    'api_secret' => env('GMO_COIN_API_SECRET', ''),
];
