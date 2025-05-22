<?php

namespace Backend\Services\Paylab;

use Backend\Services\Paylab\Request as PaylabRequest;


class PaylabRest
{
    public static function request(...$args)
    {
        return (new PaylabRequest)->request(...$args);
    }
}
