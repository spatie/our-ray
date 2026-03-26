<?php

namespace Spatie\OurRay;

use Spatie\Ray\Ray;

class OurRay
{
    /** @return Ray */
    public function ray(...$args)
    {
        $instance = ray();

        CloudState::enable($instance->uuid);

        if (count($args)) {
            return $instance->send(...$args);
        }

        return $instance;
    }
}
