<?php

namespace Backend\Services\Cademi;

use Backend\Services\Cademi\Request as CademiRequest;

class CademiRest
{
    public static function request(...$args)
    {
        return (new CademiRequest)->request(...$args);
    }
}
