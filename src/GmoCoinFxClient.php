<?php

namespace GmoCoin;

class GmoCoinFxClient
{
    private $endpoint;
    private $apiKey;
    private $apiSecret;

    public function __construct(string $endpoint, string $apiKey = '', string $apiSecret = '')
    {
        $this->endpoint = rtrim($endpoint, '/');
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    public function request(string $method, string $path, array $params = [], array $body = [])
    {
        $url = $this->endpoint . $path;
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $headers = [
            'Content-Type: application/json'
        ];

        $payload = json_encode($body, JSON_UNESCAPED_UNICODE);

        if ($this->apiKey && $this->apiSecret) {
            $timestamp = (string) round(microtime(true) * 1000);
            $text = $timestamp . $method . $path . ($payload === 'null' ? '' : $payload);
            $sign = hash_hmac('sha256', $text, $this->apiSecret);

            $headers[] = 'API-KEY: ' . $this->apiKey;
            $headers[] = 'API-TIMESTAMP: ' . $timestamp;
            $headers[] = 'API-SIGN: ' . $sign;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }

        $response = curl_exec($ch);
        if ($response === false) {
            throw new \RuntimeException('Curl error: ' . curl_error($ch));
        }
        curl_close($ch);

        return json_decode($response, true);
    }

    public function getStatus()
    {
        return $this->request('GET', '/public/v1/status');
    }

    public function getTicker()
    {
        return $this->request('GET', '/public/v1/ticker');
    }

    public function getKlines(string $symbol, string $priceType, string $interval, string $date)
    {
        return $this->request('GET', '/public/v1/klines', [
            'symbol'    => $symbol,
            'priceType' => $priceType,
            'interval'  => $interval,
            'date'      => $date,
        ]);
    }

    public function getOrderBooks(string $symbol)
    {
        return $this->request('GET', '/public/v1/orderbooks', [
            'symbol' => $symbol,
        ]);
    }

    public function getTrades(string $symbol, int $page = 1, int $count = 100)
    {
        return $this->request('GET', '/public/v1/trades', [
            'symbol' => $symbol,
            'page'   => $page,
            'count'  => $count,
        ]);
    }

    public function getSymbols()
    {
        return $this->request('GET', '/public/v1/symbols');
    }

    public function getMargin()
    {
        return $this->request('GET', '/private/v1/account/margin');
    }

    public function getAssets()
    {
        return $this->request('GET', '/private/v1/account/assets');
    }

    public function getTradingVolume()
    {
        return $this->request('GET', '/private/v1/account/tradingVolume');
    }

    public function getFiatDepositHistory()
    {
        return $this->request('GET', '/private/v1/account/fiatDeposit/history');
    }

    public function getFiatWithdrawalHistory()
    {
        return $this->request('GET', '/private/v1/account/fiatWithdrawal/history');
    }

    public function getDepositHistory()
    {
        return $this->request('GET', '/private/v1/account/deposit/history');
    }

    public function getWithdrawalHistory()
    {
        return $this->request('GET', '/private/v1/account/withdrawal/history');
    }

    public function getOrders(array $params)
    {
        return $this->request('GET', '/private/v1/orders', $params);
    }

    public function getActiveOrders(array $params = [])
    {
        return $this->request('GET', '/private/v1/activeOrders', $params);
    }

    public function getExecutions(array $params)
    {
        return $this->request('GET', '/private/v1/executions', $params);
    }

    public function getLatestExecutions(array $params)
    {
        return $this->request('GET', '/private/v1/latestExecutions', $params);
    }

    public function getOpenPositions(array $params = [])
    {
        return $this->request('GET', '/private/v1/openPositions', $params);
    }

    public function getPositionSummary()
    {
        return $this->request('GET', '/private/v1/positionSummary');
    }

    public function speedOrder(array $body)
    {
        return $this->request('POST', '/private/v1/speedOrder', [], $body);
    }

    public function order(array $body)
    {
        return $this->request('POST', '/private/v1/order', [], $body);
    }

    public function ifdOrder(array $body)
    {
        return $this->request('POST', '/private/v1/ifdOrder', [], $body);
    }

    public function ifoOrder(array $body)
    {
        return $this->request('POST', '/private/v1/ifoOrder', [], $body);
    }

    public function changeOrder(array $body)
    {
        return $this->request('POST', '/private/v1/changeOrder', [], $body);
    }

    public function changeOcoOrder(array $body)
    {
        return $this->request('POST', '/private/v1/changeOcoOrder', [], $body);
    }

    public function changeIfdOrder(array $body)
    {
        return $this->request('POST', '/private/v1/changeIfdOrder', [], $body);
    }

    public function changeIfoOrder(array $body)
    {
        return $this->request('POST', '/private/v1/changeIfoOrder', [], $body);
    }

    public function cancelOrders(array $body)
    {
        return $this->request('POST', '/private/v1/cancelOrders', [], $body);
    }

    public function cancelBulkOrder(array $body)
    {
        return $this->request('POST', '/private/v1/cancelBulkOrder', [], $body);
    }

    public function closeOrder(array $body)
    {
        return $this->request('POST', '/private/v1/closeOrder', [], $body);
    }
}

