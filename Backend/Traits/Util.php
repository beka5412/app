<?php

namespace Backend\Traits;

trait Util
{
    private function subdomains()
    {
        $subdomains = [];
        foreach ($this->application->routes as $key => $value)
        {
            if ($key == ".") continue;
            $subdomains[$key] = $value;
        }
        return $subdomains;
    }
}