<?php

use Spatie\OurRay\OurRay;

if (function_exists('arch')) {
    arch('will not use debugging functions')
        ->expect(['dd', 'dump', 'ray'])
        ->not->toBeUsed()
        ->ignoring(OurRay::class);
}
