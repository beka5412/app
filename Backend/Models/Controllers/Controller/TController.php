<?php

namespace Backend\Controllers\Controller;

use Backend\App;

trait TController
{
    public function __construct(public App $application)
    {

    }
}