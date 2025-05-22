<?php

namespace Backend\Services\Sellflux;

use Backend\Services\Sellflux\Request as SellfluxRequest;


class SellfluxRest
{
    public static function request(...$args)
    {
        return (new SellfluxRequest)->request(...$args);
    }
}
