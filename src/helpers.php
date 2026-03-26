<?php

use Spatie\OurRay\CloudClient;
use Spatie\OurRay\CloudState;
use Spatie\OurRay\OurRay;
use Spatie\Ray\Ray;
use Spatie\Ray\Request;
use Spatie\Ray\Settings\SettingsFactory;

Ray::macro('cloud', function () {
    CloudState::enable($this->uuid);

    return $this;
});

Ray::$afterSendCallbacks[] = function (Ray $ray, Request $request) {
    if (CloudState::isEnabled($ray->uuid) && CloudState::client()) {
        CloudState::client()->send($request);
    }
};

$settings = SettingsFactory::createFromConfigFile();
$cloudEndpoint = $settings->cloud_endpoint ?? 'https://ourray.app/api';
CloudState::setClient(new CloudClient($cloudEndpoint));

if (! function_exists('our')) {
    function our(): OurRay
    {
        return new OurRay;
    }
}
