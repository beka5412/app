<?php 

namespace Backend\Services\RocketMember;

// use Backend\App;
use Backend\Http\Request;
use Backend\Services\RocketMember\Service;

class RocketMember
{
    public static function __callStatic($name, $arguments)
    {
        $instance = new Service;
        $instance->url = env('ROCKETMEMBER_WEBHOOK');
        return $instance->$name(...$arguments);  
    }
}