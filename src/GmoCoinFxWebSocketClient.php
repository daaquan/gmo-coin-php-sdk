<?php

namespace GmoCoin;

use WebSocket\Client;
use Exception;

class GmoCoinFxWebSocketClient
{
    private Client $client;
    private string $url = 'wss://api.coin.z.com/ws/public';

    public function __construct()
    {
        try {
            $this->client = new Client($this->url);
        } catch (Exception $e) {
            throw new Exception('WebSocket connection failed: ' . $e->getMessage());
        }
    }

    public function subscribeTicker(string $symbol = 'BTC_JPY')
    {
        $subscriptionMessage = json_encode([
            'command' => 'subscribe',
            'channel' => 'ticker',
            'symbol' => $symbol
        ]);

        try {
            $this->client->send($subscriptionMessage);
        } catch (Exception $e) {
            throw new Exception('Failed to send subscription message: ' . $e->getMessage());
        }
    }

    public function receiveMessage()
    {
        try {
            $message = $this->client->receive();
            $parsedMessage = json_decode($message, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Failed to parse JSON message: ' . json_last_error_msg());
            }
            return $parsedMessage;
        } catch (Exception $e) {
            throw new Exception('Failed to receive message: ' . $e->getMessage());
        }
    }

    public function close()
    {
        try {
            $this->client->close();
        } catch (Exception $e) {
            // Log or handle close error if needed
        }
    }
}