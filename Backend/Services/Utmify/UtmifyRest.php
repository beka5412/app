<?php

namespace Backend\Services\Utmify;

use Backend\Services\Utmify\Request as UtmifyRequest;


class UtmifyRest
{
    public static function request(...$args)
    {
        return (new UtmifyRequest)->request(...$args);
    }
}
