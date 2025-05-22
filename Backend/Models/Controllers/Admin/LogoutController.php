<?php

namespace Backend\Controllers\Admin;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Link;
use Backend\Controllers\Public\AdminLoginController;

class LogoutController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
    }

    public function index(Request $request)
    {
        unset($_SESSION[env('ADMIN_AUTH_KEY')]);
        $login = new AdminLoginController($this->application);
        $login->index($request);
        Link::changeUrl(site_url(), '/admin/login');
    }
}