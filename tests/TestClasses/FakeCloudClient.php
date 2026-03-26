<?php

namespace Spatie\OurRay\Tests\TestClasses;

use Spatie\OurRay\CloudClient;
use Spatie\Ray\Request;

class FakeCloudClient extends CloudClient
{
    /** @var array */
    protected $sentRequests = [];

    public function __construct()
    {
        parent::__construct();
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
