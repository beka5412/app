<?php

namespace Backend\Controllers\Admin\Balance;

use Backend\App;
use Backend\Http\Request;
use Backend\Template\View;
use Backend\Models\Administrator;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Balance';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/admin/balance/indexView.php';
        $this->admin = admin();
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;
        View::render($this->indexFile, compact('context', 'title', 'admin'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;
        View::response($this->indexFile, compact('context', 'title', 'admin'));
    }
}