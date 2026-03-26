<?php

use Spatie\OurRay\CloudState;
use Spatie\OurRay\Tests\TestClasses\FakeCloudClient;
use Spatie\OurRay\Tests\TestClasses\ThrowingCloudClient;
use Spatie\Ray\Ray;
use Spatie\Ray\Request;
use Spatie\Ray\Settings\SettingsFactory;
use Spatie\OurRay\Tests\TestClasses\FakeClient;

beforeEach(function () {
    $this->fakeClient = new FakeClient();
    $this->fakeCloudClient = new FakeCloudClient();
    $this->settings = SettingsFactory::createFromConfigFile();

    $this->ray = new Ray($this->settings, $this->fakeClient, 'fakeUuid');
    $this->ray->enable();

    Ray::rateLimiter()->clear();

    CloudState::clear();
    CloudState::setClient($this->fakeCloudClient);

    Ray::$afterSendCallbacks = [];
    Ray::$afterSendCallbacks[] = function (Ray $ray, Request $request) {
        if (CloudState::isEnabled($ray->uuid) && CloudState::client()) {
            CloudState::client()->send($request);
        }
    };
});

afterEach(function () {
    CloudState::clear();
    Ray::$afterSendCallbacks = [];
});

it('cloud() returns the ray instance for fluent chaining', function () {
    $result = $this->ray->cloud();

    expect($result)->toBe($this->ray);
});

it('sends to both local and cloud when cloud() is called', function () {
    $this->ray->cloud()->send('test');

    expect($this->fakeClient->sentRequests())->toHaveCount(1);
    expect($this->fakeCloudClient->sentRequests())->toHaveCount(1);
});

it('only sends to local when cloud() is not called', function () {
    $this->ray->send('test');

    expect($this->fakeClient->sentRequests())->toHaveCount(1);
    expect($this->fakeCloudClient->sentRequests())->toHaveCount(0);
});

it('sends to both via our()->ray() with arguments', function () {
    $ourRay = our();
    $ray = $ourRay->ray('test');

    expect($this->fakeCloudClient->sentRequests())->toHaveCount(1);
});

it('sends to both via our()->ray()->send()', function () {
    our()->ray()->send('test');

    expect($this->fakeCloudClient->sentRequests())->toHaveCount(1);
});

it('does not break local send when cloud client throws', function () {
    CloudState::clear();
    CloudState::setClient(new ThrowingCloudClient());

    $this->ray->cloud()->send('test');

    expect($this->fakeClient->sentRequests())->toHaveCount(1);
});

it('can clear cloud state', function () {
    CloudState::enable('uuid-1');
    expect(CloudState::isEnabled('uuid-1'))->toBeTrue();

    CloudState::clear();
    expect(CloudState::isEnabled('uuid-1'))->toBeFalse();
    expect(CloudState::client())->toBeNull();
});

it('tracks multiple uuids independently', function () {
    CloudState::enable('uuid-a');

    expect(CloudState::isEnabled('uuid-a'))->toBeTrue();
    expect(CloudState::isEnabled('uuid-b'))->toBeFalse();

    CloudState::enable('uuid-b');

    expect(CloudState::isEnabled('uuid-a'))->toBeTrue();
    expect(CloudState::isEnabled('uuid-b'))->toBeTrue();
});
