<?php

namespace Spatie\OurRay;

class CloudState
{
    /** @var array<string, bool> */
    protected static $enabledUuids = [];

    /** @var CloudClient|null */
    protected static $client = null;

    public static function enable(string $uuid): void
    {
        static::$enabledUuids[$uuid] = true;
    }

    public static function isEnabled(string $uuid): bool
    {
        return isset(static::$enabledUuids[$uuid]);
    }

    public static function setClient(CloudClient $client): void
    {
        static::$client = $client;
    }

    public static function client(): ?CloudClient
    {
        return static::$client;
    }

    public static function clear(): void
    {
        static::$enabledUuids = [];
        static::$client = null;
    }
}
