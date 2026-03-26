<?php

namespace Spatie\OurRay;

use Spatie\Ray\Ray;

class OurRay
{
    /** @return Ray */
    public function ray(...$args)
    {
        return ray(...$args)->cloud();
    }
}
