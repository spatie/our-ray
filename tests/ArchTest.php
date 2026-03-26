<?php

use Spatie\OurRay\OurRay;

if (version_compare(PHP_VERSION, '8.2.0', '>=')) {
    it('will not use debugging functions')
        ->expect(['dd', 'dump', 'ray'])
        ->not->toBeUsed()
        ->ignoring(OurRay::class);
}
