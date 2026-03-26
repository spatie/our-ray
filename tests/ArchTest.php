<?php

use Spatie\OurRay\OurRay;

it('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->not->toBeUsed()
    ->ignoring(OurRay::class);
