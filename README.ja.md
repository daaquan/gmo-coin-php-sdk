# gmo-coin-php-sdk

[GMOコインAPI](https://api.coin.z.com/docs/#outline) をLaravelから扱うためのSDKです。暗号資産とFXの両エンドポイントに対応しています。

## 特徴

- 暗号資産APIとFX APIを簡単に呼び出せるFacadeを提供
- APIキーを設定するとリクエスト署名を自動で付与
- PHP 8.1以上、Laravel 12に対応

## インストール

Composerでパッケージを追加します:

```bash
composer require gmo-coin/gmo-coin-php-sdk
```

設定ファイルを公開します:

```bash
php artisan vendor:publish --tag=config
```

`.env`に認証情報を設定します:

```
GMO_COIN_CRYPTO_API_KEY=your_crypto_key
GMO_COIN_CRYPTO_API_SECRET=your_crypto_secret
GMO_COIN_FX_API_KEY=your_fx_key
GMO_COIN_FX_API_SECRET=your_fx_secret
```

## 使い方

Facadeを利用してAPIエンドポイントを呼び出します。

```php
use GmoCoin\Facades\GmoCoin;
use GmoCoin\Facades\GmoCoinFx;

$response = GmoCoin::getStatus();
$ticker   = GmoCoin::getTicker();
$klines   = GmoCoin::getKlines('BTC', 'ASK', '1min', '20231028');
$books    = GmoCoin::getOrderBooks('BTC');
$assets   = GmoCoin::getAssets();
```

APIキーを設定すると、リクエストは自動的に署名されます。

## 開発

静的解析を実行してコードベースを保守します:

```bash
composer phpstan
```
