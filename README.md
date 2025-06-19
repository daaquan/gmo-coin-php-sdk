# gmo-coin-php-sdk

Laravel wrapper for the [GMO Coin APIs](https://api.coin.z.com/docs/#outline).
It supports both the cryptocurrency and FX endpoints.

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

Use the provided facades to call API endpoints.

```php
use GmoCoin\Facades\GmoCoin;
use GmoCoin\Facades\GmoCoinFx;

$response = GmoCoin::getStatus();
$ticker   = GmoCoin::getTicker();
$klines   = GmoCoin::getKlines('BTC', 'ASK', '1min', '20231028');
$books    = GmoCoin::getOrderBooks('BTC');
$assets   = GmoCoin::getAssets();
```

The client automatically signs requests when API keys are configured.

