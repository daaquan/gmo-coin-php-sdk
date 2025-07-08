<?php

namespace GmoCoin\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade exposing {@see \GmoCoin\GmoCoinFxClient} methods.
 */
class GmoCoinFx extends Facade
{
    /**
     * Resolve the underlying service from the container.
     */
    protected static function getFacadeAccessor(): string
    {
        return \GmoCoin\GmoCoinFxClient::class;
    }
}

