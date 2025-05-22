<?php

namespace Backend\Notifiers\Email;

use Backend\Notifiers\Email\SDK;

class Mailer
{
    public static function __callStatic($name, $arguments)
    {
        $instance = new SDK;
        return $instance->$name(...$arguments);  
    }
}