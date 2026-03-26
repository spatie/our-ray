<?php

namespace Spatie\OurRay\Tests\TestClasses;

use Spatie\Ray\Client;
use Spatie\Ray\Request;

class FakeClient extends Client
{
    /** @var array */
    protected $sentRequests = [];

    public function __construct()
    {
        parent::__construct(23517, 'localhost');
    }

    public function serverIsAvailable(): bool
    {
        return true;
    }

    public function send(Request $request): void
    {
        $this->sentRequests[] = $request->toArray();
    }

    public function sentRequests(): array
    {
        return $this->sentRequests;
    }

    public function reset(): void
    {
        $this->sentRequests = [];
    }
}
