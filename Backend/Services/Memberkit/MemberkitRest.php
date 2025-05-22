<?php

namespace Backend\Services\Memberkit;

use Backend\Services\Memberkit\Request as MemberkitRequest;


class MemberkitRest
{
    public static function request(...$args)
    {
        return (new MemberkitRequest)->request(...$args);
    }
}
