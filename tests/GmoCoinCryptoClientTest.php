<?php
use PHPUnit\Framework\TestCase;

class DummyCryptoClient extends GmoCoin\GmoCoinCryptoClient
{
    public $calls = [];
    public function request(string $method, string $path, array $params = [], array $body = []): array
    {
        $this->calls[] = [$method, $path, $params, $body];
        return ['ok' => true];
    }
}

class GmoCoinCryptoClientTest extends TestCase
{
    /**
     * @dataProvider apiProvider
     */
    public function testApiCalls($method, $args, $expected)
    {
        $client = new DummyCryptoClient('https://example.com');
        call_user_func_array([$client, $method], $args);
        $this->assertNotEmpty($client->calls);
        [$m, $path, $params, $body] = $client->calls[0];
        $this->assertSame($expected['method'], $m);
        $this->assertSame($expected['path'], $path);
        $this->assertSame($expected['params'], $params);
        $this->assertSame($expected['body'], $body);
    }

    public static function apiProvider()
    {
        return [
            ['getStatus', [], ['method' => 'GET', 'path' => '/public/v1/status', 'params' => [], 'body' => []]],
            ['getTicker', [], ['method' => 'GET', 'path' => '/public/v1/ticker', 'params' => [], 'body' => []]],
            ['getKlines', ['BTC', 'ASK', '1min', '20240101'], ['method' => 'GET', 'path' => '/public/v1/klines', 'params' => ['symbol' => 'BTC', 'priceType' => 'ASK', 'interval' => '1min', 'date' => '20240101'], 'body' => []]],
            ['getOrderBooks', ['BTC'], ['method' => 'GET', 'path' => '/public/v1/orderbooks', 'params' => ['symbol' => 'BTC'], 'body' => []]],
            ['getTrades', ['BTC', 1, 50], ['method' => 'GET', 'path' => '/public/v1/trades', 'params' => ['symbol' => 'BTC', 'page' => 1, 'count' => 50], 'body' => []]],
            ['getSymbols', [], ['method' => 'GET', 'path' => '/public/v1/symbols', 'params' => [], 'body' => []]],
            ['getMargin', [], ['method' => 'GET', 'path' => '/private/v1/account/margin', 'params' => [], 'body' => []]],
            ['getAssets', [], ['method' => 'GET', 'path' => '/private/v1/account/assets', 'params' => [], 'body' => []]],
            ['getTradingVolume', [], ['method' => 'GET', 'path' => '/private/v1/account/tradingVolume', 'params' => [], 'body' => []]],
            ['getFiatDepositHistory', [], ['method' => 'GET', 'path' => '/private/v1/account/fiatDeposit/history', 'params' => [], 'body' => []]],
            ['getFiatWithdrawalHistory', [], ['method' => 'GET', 'path' => '/private/v1/account/fiatWithdrawal/history', 'params' => [], 'body' => []]],
            ['getDepositHistory', [], ['method' => 'GET', 'path' => '/private/v1/account/deposit/history', 'params' => [], 'body' => []]],
            ['getWithdrawalHistory', [], ['method' => 'GET', 'path' => '/private/v1/account/withdrawal/history', 'params' => [], 'body' => []]],
            ['getOrders', [['orderId' => '1']], ['method' => 'GET', 'path' => '/private/v1/orders', 'params' => ['orderId' => '1'], 'body' => []]],
            ['getActiveOrders', [['symbol' => 'BTC']], ['method' => 'GET', 'path' => '/private/v1/activeOrders', 'params' => ['symbol' => 'BTC'], 'body' => []]],
            ['getExecutions', [['symbol' => 'BTC']], ['method' => 'GET', 'path' => '/private/v1/executions', 'params' => ['symbol' => 'BTC'], 'body' => []]],
            ['getLatestExecutions', [['symbol' => 'BTC']], ['method' => 'GET', 'path' => '/private/v1/latestExecutions', 'params' => ['symbol' => 'BTC'], 'body' => []]],
            ['getOpenPositions', [['symbol' => 'BTC']], ['method' => 'GET', 'path' => '/private/v1/openPositions', 'params' => ['symbol' => 'BTC'], 'body' => []]],
            ['getPositionSummary', [], ['method' => 'GET', 'path' => '/private/v1/positionSummary', 'params' => [], 'body' => []]],
            ['speedOrder', [['symbol' => 'BTC']], ['method' => 'POST', 'path' => '/private/v1/speedOrder', 'params' => [], 'body' => ['symbol' => 'BTC']]],
            ['order', [['symbol' => 'BTC']], ['method' => 'POST', 'path' => '/private/v1/order', 'params' => [], 'body' => ['symbol' => 'BTC']]],
            ['ifdOrder', [['symbol' => 'BTC']], ['method' => 'POST', 'path' => '/private/v1/ifdOrder', 'params' => [], 'body' => ['symbol' => 'BTC']]],
            ['ifoOrder', [['symbol' => 'BTC']], ['method' => 'POST', 'path' => '/private/v1/ifoOrder', 'params' => [], 'body' => ['symbol' => 'BTC']]],
            ['changeOrder', [['orderId' => 1]], ['method' => 'POST', 'path' => '/private/v1/changeOrder', 'params' => [], 'body' => ['orderId' => 1]]],
            ['changeOcoOrder', [['orderId' => 1]], ['method' => 'POST', 'path' => '/private/v1/changeOcoOrder', 'params' => [], 'body' => ['orderId' => 1]]],
            ['changeIfdOrder', [['orderId' => 1]], ['method' => 'POST', 'path' => '/private/v1/changeIfdOrder', 'params' => [], 'body' => ['orderId' => 1]]],
            ['changeIfoOrder', [['orderId' => 1]], ['method' => 'POST', 'path' => '/private/v1/changeIfoOrder', 'params' => [], 'body' => ['orderId' => 1]]],
            ['cancelOrders', [['orderId' => 1]], ['method' => 'POST', 'path' => '/private/v1/cancelOrders', 'params' => [], 'body' => ['orderId' => 1]]],
            ['cancelBulkOrder', [['symbol' => 'BTC']], ['method' => 'POST', 'path' => '/private/v1/cancelBulkOrder', 'params' => [], 'body' => ['symbol' => 'BTC']]],
            ['closeOrder', [['orderId' => 1]], ['method' => 'POST', 'path' => '/private/v1/closeOrder', 'params' => [], 'body' => ['orderId' => 1]]],
        ];
    }
}
