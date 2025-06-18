<?php

namespace GmoCoin;

class GmoCoinClient
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
}

