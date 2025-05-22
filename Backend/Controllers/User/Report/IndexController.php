<?php

namespace Backend\Controllers\User\Report;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Product;
use Backend\Exceptions\Product\ProductNotFoundException;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Reports';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/reports/indexView.php';
        $this->user = user();
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $products = Product::where('user_id', $user->id)->orderBy('id', 'DESC')->paginate(10);
        View::render($this->indexFile, compact('title', 'context', 'user', 'products'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $products = Product::where('user_id', $user->id)->orderBy('id', 'DESC')->paginate(10);
        View::response($this->indexFile, compact('title', 'context', 'user', 'products'));
    }
}