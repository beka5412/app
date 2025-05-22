<?php

namespace Backend\Controllers\Browser\Subdomains\Checkout\Dashboard;

use Backend\App;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Http\Request;

class NotFoundController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Not Found';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/browser/subdomains/checkout/dashboard/404NotFoundView.php';
        $this->user = user();
    }

    public function view(Request $request)
    {
        $title = $this->title;        
        $context = $this->context;
        $user = $this->user;
        View::render($this->indexFile, compact('context', 'title', 'user'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        View::response($this->indexFile, compact('context', 'title', 'user'));
    }
}