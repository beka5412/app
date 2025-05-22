<?php

namespace Backend\Services\Astronmembers;

use Backend\Services\Astronmembers\Request as AstronmembersRequest;


class AstronmembersRest
{
    public static function request(...$args)
    {
        return (new AstronmembersRequest)->request(...$args);
    }
}
