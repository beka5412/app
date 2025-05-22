<?php

namespace Backend\Services\Iugu;

use Backend\Services\Iugu\Request as IuguRequest;

class IuguRest
{
    public static function request(...$args)
    {
        return (new IuguRequest)->request(...$args);
    }
}
