<?php

return [
    // Default endpoints for each API
    'crypto_endpoint' => env('GMO_COIN_CRYPTO_ENDPOINT', 'https://api.coin.z.com'),
    'fx_endpoint'     => env('GMO_COIN_FX_ENDPOINT', 'https://forex-api.coin.z.com'),

    // Legacy endpoint key kept for backwards compatibility
    'endpoint' => env('GMO_COIN_ENDPOINT', env('GMO_COIN_CRYPTO_ENDPOINT', 'https://api.coin.z.com')),

    'api_key'    => env('GMO_COIN_API_KEY', ''),
    'api_secret' => env('GMO_COIN_API_SECRET', ''),
];

