<?php

namespace Backend\Controllers\User\App\UTMify;

use Backend\App;
use Backend\Http\Request;
use Backend\Template\View;
use Backend\Models\User;

class IndexController
{
    public App $application;
    public User $user;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->user = user();
    }
}
