<?php

namespace Backend\Attributes;

#[Attribute]
class Route
{
    public function __construct(string $verb, string $resouce, string $subdomain="", array $config=[]) {

    }
}