<?php

namespace Spatie\OurRay;

use Spatie\Ray\Request;

class CloudClient
{
    /** @var string */
    protected $endpoint;

    public function __construct(string $endpoint = 'https://ourray.app/api')
    {
        $this->endpoint = $endpoint;
    }

    public function send(Request $request): void
    {
        try {
            $data = $request->toArray();

            $data['payloads'] = array_values(array_filter(
                array_map([DumbifyPayload::class, 'dumbify'], $data['payloads'])
            ));

            if (empty($data['payloads'])) {
                return;
            }

            $ch = curl_init($this->endpoint);

            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                ],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 2,
                CURLOPT_CONNECTTIMEOUT => 2,
            ]);

            curl_exec($ch);
            curl_close($ch);
        } catch (\Throwable $e) {
        }
    }
}
