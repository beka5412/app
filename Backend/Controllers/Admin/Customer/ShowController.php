<?php

namespace Backend\Controllers\Admin\Customer;

use Backend\App;
use Backend\Http\Request;
use Backend\Template\View;
use Backend\Models\Administrator;
use Backend\Models\User;

class ShowController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Customer';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/admin/customers/showView.php';
        $this->admin = admin();
    }

    public function index(Request $request, $id)
    {
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;
        $user = User::where('id', $id)->first();
        View::render($this->indexFile, compact('context', 'title', 'admin', 'user'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;
        $user = User::where('id', $id)->first();
        View::response($this->indexFile, compact('context', 'title', 'admin', 'user'));
    }
}