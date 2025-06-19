<?php

namespace GmoCoin\Facades;

use Illuminate\Support\Facades\Facade;

class GmoCoinFx extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \GmoCoin\GmoCoinFxClient::class;
    }
}

