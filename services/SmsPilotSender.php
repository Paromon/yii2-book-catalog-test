<?php

declare(strict_types=1);

namespace app\services;

use RuntimeException;
use yii\httpclient\Client;
use yii\httpclient\Exception as HttpClientException;

final class SmsPilotSender implements SmsSenderInterface
{
    public function __construct(
        private readonly Client $client,
        private readonly string $apiKey,
        private readonly string $apiUrl = 'https://smspilot.ru/api.php',
    ) {
    }

    public function send(string $phone, string $message): void
    {
        if ($this->apiKey === '') {
            throw new RuntimeException('SMS API key is not configured.');
        }

        try {
            $response = $this->client
                ->createRequest()
                ->setMethod('POST')
                ->setUrl($this->apiUrl)
                ->setData([
                    'send' => $message,
                    'to' => $phone,
                    'apikey' => $this->apiKey,
                    'format' => 'json',
                ])
                ->send();
        } catch (HttpClientException $e) {
            throw new RuntimeException('SMS provider request failed.', 0, $e);
        }

        if (!$response->isOk) {
            throw new RuntimeException('SMS provider HTTP error: ' . $response->statusCode);
        }

        $data = $response->data;
        if (!is_array($data) || isset($data['error'])) {
            $code = 'unknown';
            if (is_array($data['error'] ?? null) && isset($data['error']['code'])) {
                $code = (string) $data['error']['code'];
            }
            throw new RuntimeException('SMS provider returned an error. Code: ' . $code);
        }
    }
}
