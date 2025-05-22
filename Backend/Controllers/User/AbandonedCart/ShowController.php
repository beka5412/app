<?php

namespace Backend\Controllers\User\AbandonedCart;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\User;

class ShowController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Show Customers';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/abandoned_carts/showView.php';
        $this->user = user();
    }

    public function index(Request $request, $id)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        View::render($this->indexFile, compact('title', 'context', 'user'));
    }

    public function element(Request $request)
    {
        $body = $request->pageParams();
        $id = $body?->id;
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        View::response($this->indexFile, compact('title', 'context', 'user'));
    }
}