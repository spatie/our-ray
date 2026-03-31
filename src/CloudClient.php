<?php

namespace Spatie\OurRay;

use Spatie\Ray\Request;

class CloudClient
{
    /** @var string */
    protected $endpoint;

    /** @var array<int, array<string, mixed>> */
    protected $buffer = [];

    /** @var bool */
    protected $shutdownRegistered = false;

    /** @var int */
    protected $batchSize = 5;

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

            $this->buffer[] = $data;

            $this->registerShutdown();

            if (count($this->buffer) >= $this->batchSize) {
                $this->flush();
            }
        } catch (\Throwable $e) {
        }
    }

    public function flush(): void
    {
        try {
            if (empty($this->buffer)) {
                return;
            }

            $batch = $this->buffer;
            $this->buffer = [];

            $ch = curl_init($this->endpoint);

            if ($ch === false) {
                return;
            }

            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($batch),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                ],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 5,
                CURLOPT_CONNECTTIMEOUT => 5,
            ]);

            curl_exec($ch);
            curl_close($ch);
        } catch (\Throwable $e) {
        }
    }

    protected function registerShutdown(): void
    {
        if ($this->shutdownRegistered) {
            return;
        }

        $this->shutdownRegistered = true;

        register_shutdown_function(function () {
            $this->flush();
        });
    }
}
