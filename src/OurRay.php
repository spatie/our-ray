<?php

namespace Spatie\OurRay;

use Spatie\Ray\Ray;
use Spatie\Ray\Settings\SettingsFactory;

class OurRay
{
    /** @return Ray */
    public function ray(...$args)
    {
        $settings = SettingsFactory::createFromConfigFile();

        $ray = new Ray($settings);

        CloudState::enable($ray->uuid);

        if (count($args)) {
            return $ray->send(...$args);
        }

        return $ray;
    }
}
