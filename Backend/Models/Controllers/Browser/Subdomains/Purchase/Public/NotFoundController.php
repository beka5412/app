<?php

namespace Backend\Controllers\Browser\Subdomains\Purchase\Public;

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
        $this->context = 'public';
        $this->indexFile = 'frontend/view/browser/subdomains/purchase/public/404NotFoundView.php';
    }

    public function view(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        View::render($this->indexFile, compact('context', 'title'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        View::response($this->indexFile, compact('context', 'title'));
    }
}