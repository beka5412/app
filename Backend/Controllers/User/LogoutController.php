<?php

namespace Backend\Controllers\User;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Link;
use Backend\Controllers\Public\LoginController;

class LogoutController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
    }

    public function index(Request $request)
    {
        unset($_SESSION[env('USER_AUTH_KEY')]);
        // $login = new LoginController($this->application);
        // $login->index($request);
        
        // Link::changeUrl(site_url(), '/login');
        header("location: /login");
    }
}