<?php

namespace Spatie\OurRay\Tests\TestClasses;

use Spatie\Ray\Request;

class ThrowingCloudClient extends FakeCloudClient
{
    public function send(Request $request): void
    {
        throw new \RuntimeException('Cloud is down');
    }
}
