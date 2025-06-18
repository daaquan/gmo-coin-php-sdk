<?php

namespace GmoCoin\Facades;

use Illuminate\Support\Facades\Facade;

class GmoCoin extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \GmoCoin\GmoCoinClient::class;
    }
}

