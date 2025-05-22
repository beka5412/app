<?php

namespace Backend\Services\NoxPay;

use Backend\Services\NoxPay\Request as NoxPayRequest;

class NoxPayRest
{
    public static function request(...$args)
    {
        return (new NoxPayRequest)->request(...$args);
    }
}