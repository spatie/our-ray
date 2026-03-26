<?php

namespace Spatie\OurRay;

use Spatie\Ray\Request;

class CloudClient
{
    /** @var string */
    protected $apiKey;

    /** @var string */
    protected $endpoint;

    public function __construct(string $apiKey, string $endpoint = 'https://ourray.app/api')
    {
        $this->apiKey = $apiKey;

        $this->endpoint = $endpoint;
    }

    public function send(Request $request): void
    {
        try {
            $ch = curl_init($this->endpoint);

            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $request->toJson(),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    "Authorization: Bearer {$this->apiKey}",
                ],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 2,
                CURLOPT_CONNECTTIMEOUT => 2,
            ]);

            curl_exec($ch);
            curl_close($ch);
        } catch (\Throwable $e) {
            // swallow all exceptions
        }
    }
}
