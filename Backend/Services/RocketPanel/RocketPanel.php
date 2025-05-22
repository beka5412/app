<?php 

namespace Backend\Services\RocketPanel;

// use Backend\App;
use Backend\Http\Request;
use Backend\Services\RocketPanel\Service;

class RocketPanel
{
    public static function __callStatic($name, $arguments)
    {
        $instance = new Service;
        $instance->url = env('ROCKETPANEL_WEBHOOK');
        return $instance->$name(...$arguments);  
    }
}