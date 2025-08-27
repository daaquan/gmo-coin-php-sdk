<?php

namespace GmoCoin;

/**
 * Client for interacting with the GMO Coin FX API.
 * This class encapsulates all REST requests made to the FX endpoint.
 */
class GmoCoinFxClient
{
    /** @var string Base URL for the API */
    private string $endpoint;

    /** @var string API key used for authenticated requests */
    private string $apiKey;

    /** @var string API secret used to sign requests */
    private string $apiSecret;

    /** @var array<string, string> Mapping of paths to endpoint types */
    private array $endpointMap = [
        // Public endpoints
        '/v1/status' => 'public',
        '/v1/ticker' => 'public',
        '/v1/klines' => 'public',
        '/v1/orderbooks' => 'public',
        '/v1/trades' => 'public',
        '/v1/symbols' => 'public',
        
        // Private endpoints
        '/v1/account/margin' => 'private',
        '/v1/account/assets' => 'private',
        '/v1/account/tradingVolume' => 'private',
        '/v1/account/fiatDeposit/history' => 'private',
        '/v1/account/fiatWithdrawal/history' => 'private',
        '/v1/account/deposit/history' => 'private',
        '/v1/account/withdrawal/history' => 'private',
        '/v1/orders' => 'private',
        '/v1/activeOrders' => 'private',
        '/v1/executions' => 'private',
        '/v1/latestExecutions' => 'private',
        '/v1/openPositions' => 'private',
        '/v1/positionSummary' => 'private',
        '/v1/speedOrder' => 'private',
        '/v1/order' => 'private',
        '/v1/ifdOrder' => 'private',
        '/v1/ifoOrder' => 'private',
        '/v1/changeOrder' => 'private',
        '/v1/changeOcoOrder' => 'private',
        '/v1/changeIfdOrder' => 'private',
        '/v1/changeIfoOrder' => 'private',
        '/v1/cancelOrders' => 'private',
        '/v1/cancelBulkOrder' => 'private',
        '/v1/closeOrder' => 'private',
    ];

    /**
     * Create a new API client instance.
     *
     * @param string $endpoint   Base URL for the FX API.
     * @param string $apiKey     Optional API key for private endpoints.
     * @param string $apiSecret  Optional API secret for private endpoints.
     */
    public function __construct(string $endpoint, string $apiKey = '', string $apiSecret = '')
    {
        $this->endpoint = rtrim($endpoint, '/');
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    /**
     * Perform a HTTP request against the FX API.
     *
     * @param string               $method HTTP method to use.
     * @param string               $path   Request path starting with '/v1/'.
     * @param array<string, mixed> $params Optional query parameters.
     * @param array<string, mixed> $body   Optional JSON body for POST requests.
     *
     * @return array<string, mixed> Decoded JSON response from the API.
     */
    public function request(string $method, string $path, array $params = [], array $body = []): array
    {
        $endpointType = $this->endpointMap[$path] ?? 'public';
        $prefix = $endpointType === 'private' ? '/private' : '/public';
        $url = $this->endpoint . $prefix . $path;
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $headers = [
            'Content-Type: application/json'
        ];

        $payload = empty($body) ? '' : json_encode($body, JSON_UNESCAPED_UNICODE);

        if ($this->apiKey && $this->apiSecret && $endpointType === 'private') {
            $timestamp = (string) round(microtime(true) * 1000);
            $text = $timestamp . $method . $path . $payload;
            $sign = hash_hmac('sha256', $text, $this->apiSecret);

            $headers[] = 'API-KEY: ' . $this->apiKey;
            $headers[] = 'API-TIMESTAMP: ' . $timestamp;
            $headers[] = 'API-SIGN: ' . $sign;
        }

        $ch = curl_init($url);
        if ($ch === false) {
            throw new \RuntimeException('Unable to initialize curl');
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            $payloadString = (string) $payload;
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadString);
        }

        $response = curl_exec($ch);
        if ($response === false) {
            throw new \RuntimeException('Curl error: ' . curl_error($ch));
        }
        curl_close($ch);
        /** @var string $response */
        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        return $decoded;
    }

    /**
     * Retrieve the API service status.
     *
     * @return array<string, mixed>
     */
    public function getStatus(): array
    {
        return $this->request('GET', '/v1/status');
    }

    /**
     * Retrieve market ticker information.
     *
     * @return array<string, mixed>
     */
    public function getTicker(): array
    {
        return $this->request('GET', '/v1/ticker');
    }

    /**
     * Fetch candlestick data for a symbol.
     *
     * @param string $symbol     Trading pair.
     * @param string $priceType  Price type such as ASK/BID.
     * @param string $interval   Candle interval.
     * @param string $date       Date in YYYYMMDD format.
     *
     * @return array<string, mixed>
     */
    public function getKlines(string $symbol, string $priceType, string $interval, string $date): array
    {
        return $this->request('GET', '/v1/klines', [
            'symbol'    => $symbol,
            'priceType' => $priceType,
            'interval'  => $interval,
            'date'      => $date,
        ]);
    }

    /**
     * Retrieve the order book for a symbol.
     *
     * @param string $symbol Trading pair.
     *
     * @return array<string, mixed>
     */
    public function getOrderBooks(string $symbol): array
    {
        return $this->request('GET', '/v1/orderbooks', [
            'symbol' => $symbol,
        ]);
    }

    /**
     * Get trade history for a symbol.
     *
     * @param string $symbol Trading pair.
     * @param int    $page   Page number for pagination.
     * @param int    $count  Items per page.
     *
     * @return array<string, mixed>
     */
    public function getTrades(string $symbol, int $page = 1, int $count = 100): array
    {
        return $this->request('GET', '/v1/trades', [
            'symbol' => $symbol,
            'page'   => $page,
            'count'  => $count,
        ]);
    }

    /**
     * List all tradable symbols.
     *
     * @return array<string, mixed>
     */
    public function getSymbols(): array
    {
        return $this->request('GET', '/v1/symbols');
    }

    /**
     * Get account margin information.
     *
     * @return array<string, mixed>
     */
    public function getMargin(): array
    {
        return $this->request('GET', '/v1/account/margin');
    }

    /**
     * Fetch current account assets.
     *
     * @return array<string, mixed>
     */
    public function getAssets(): array
    {
        return $this->request('GET', '/v1/account/assets');
    }

    /**
     * Retrieve cumulative trading volume.
     *
     * @return array<string, mixed>
     */
    public function getTradingVolume(): array
    {
        return $this->request('GET', '/v1/account/tradingVolume');
    }

    /**
     * Get history of fiat deposits.
     *
     * @return array<string, mixed>
     */
    public function getFiatDepositHistory(): array
    {
        return $this->request('GET', '/v1/account/fiatDeposit/history');
    }

    /**
     * Get history of fiat withdrawals.
     *
     * @return array<string, mixed>
     */
    public function getFiatWithdrawalHistory(): array
    {
        return $this->request('GET', '/v1/account/fiatWithdrawal/history');
    }

    /**
     * Get cryptocurrency deposit history.
     *
     * @return array<string, mixed>
     */
    public function getDepositHistory(): array
    {
        return $this->request('GET', '/v1/account/deposit/history');
    }

    /**
     * Get cryptocurrency withdrawal history.
     *
     * @return array<string, mixed>
     */
    public function getWithdrawalHistory(): array
    {
        return $this->request('GET', '/v1/account/withdrawal/history');
    }

    /**
     * Retrieve order information.
     *
     * @param array<string, mixed> $params Query parameters.
     *
     * @return array<string, mixed>
     */
    public function getOrders(array $params): array
    {
        return $this->request('GET', '/v1/orders', $params);
    }

    /**
     * List active orders.
     *
     * @param array<string, mixed> $params Optional query parameters.
     *
     * @return array<string, mixed>
     */
    public function getActiveOrders(array $params = []): array
    {
        return $this->request('GET', '/v1/activeOrders', $params);
    }

    /**
     * Fetch execution history.
     *
     * @param array<string, mixed> $params Query parameters.
     *
     * @return array<string, mixed>
     */
    public function getExecutions(array $params): array
    {
        return $this->request('GET', '/v1/executions', $params);
    }

    /**
     * Fetch most recent executions.
     *
     * @param array<string, mixed> $params Query parameters.
     *
     * @return array<string, mixed>
     */
    public function getLatestExecutions(array $params): array
    {
        return $this->request('GET', '/v1/latestExecutions', $params);
    }

    /**
     * Retrieve currently open positions.
     *
     * @param array<string, mixed> $params Optional query parameters.
     *
     * @return array<string, mixed>
     */
    public function getOpenPositions(array $params = []): array
    {
        return $this->request('GET', '/v1/openPositions', $params);
    }

    /**
     * Get a summary of positions.
     *
     * @return array<string, mixed>
     */
    public function getPositionSummary(): array
    {
        return $this->request('GET', '/v1/positionSummary');
    }

    /**
     * Send a speed order request.
     *
     * @param array<string, mixed> $body Request payload.
     *
     * @return array<string, mixed>
     */
    public function speedOrder(array $body): array
    {
        return $this->request('POST', '/v1/speedOrder', [], $body);
    }

    /**
     * Place a standard order.
     *
     * @param array<string, mixed> $body Request payload.
     *
     * @return array<string, mixed>
     */
    public function order(array $body): array
    {
        return $this->request('POST', '/v1/order', [], $body);
    }

    /**
     * Place an IFD order.
     *
     * @param array<string, mixed> $body Request payload.
     *
     * @return array<string, mixed>
     */
    public function ifdOrder(array $body): array
    {
        return $this->request('POST', '/v1/ifdOrder', [], $body);
    }

    /**
     * Place an IFO order.
     *
     * @param array<string, mixed> $body Request payload.
     *
     * @return array<string, mixed>
     */
    public function ifoOrder(array $body): array
    {
        return $this->request('POST', '/v1/ifoOrder', [], $body);
    }

    /**
     * Amend an existing order.
     *
     * @param array<string, mixed> $body Request payload.
     *
     * @return array<string, mixed>
     */
    public function changeOrder(array $body): array
    {
        return $this->request('POST', '/v1/changeOrder', [], $body);
    }

    /**
     * Amend an existing OCO order.
     *
     * @param array<string, mixed> $body Request payload.
     *
     * @return array<string, mixed>
     */
    public function changeOcoOrder(array $body): array
    {
        return $this->request('POST', '/v1/changeOcoOrder', [], $body);
    }

    /**
     * Amend an existing IFD order.
     *
     * @param array<string, mixed> $body Request payload.
     *
     * @return array<string, mixed>
     */
    public function changeIfdOrder(array $body): array
    {
        return $this->request('POST', '/v1/changeIfdOrder', [], $body);
    }

    /**
     * Amend an existing IFO order.
     *
     * @param array<string, mixed> $body Request payload.
     *
     * @return array<string, mixed>
     */
    public function changeIfoOrder(array $body): array
    {
        return $this->request('POST', '/v1/changeIfoOrder', [], $body);
    }

    /**
     * Cancel orders by ID.
     *
     * @param array<string, mixed> $body Request payload.
     *
     * @return array<string, mixed>
     */
    public function cancelOrders(array $body): array
    {
        return $this->request('POST', '/v1/cancelOrders', [], $body);
    }

    /**
     * Cancel multiple orders at once.
     *
     * @param array<string, mixed> $body Request payload.
     *
     * @return array<string, mixed>
     */
    public function cancelBulkOrder(array $body): array
    {
        return $this->request('POST', '/v1/cancelBulkOrder', [], $body);
    }

    /**
     * Close an open position.
     *
     * @param array<string, mixed> $body Request payload.
     *
     * @return array<string, mixed>
     */
    public function closeOrder(array $body): array
    {
        return $this->request('POST', '/v1/closeOrder', [], $body);
    }
}

