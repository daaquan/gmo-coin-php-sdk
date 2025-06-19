# gmo-coin-php-sdk

Laravel wrapper for the [GMO Coin FX API](https://api.coin.z.com/fxdocs/?php#).

## Installation

Add the package using composer:

```bash
composer require gmo-coin/gmo-coin-php-sdk
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag=config
```

Set your credentials in `.env`:

```
GMO_COIN_API_KEY=your_key
GMO_COIN_API_SECRET=your_secret
```

## Usage

Use the `GmoCoin` facade to call API endpoints.

```php
use GmoCoin\Facades\GmoCoinFx;

$response = GmoCoinFx::getStatus();
$ticker   = GmoCoinFx::getTicker();
$klines   = GmoCoinFx::getKlines('USD_JPY', 'ASK', '1min', '20231028');
$books    = GmoCoinFx::getOrderBooks('USD_JPY');
$assets   = GmoCoinFx::getAssets();
```

The client automatically signs requests when API keys are configured.

