<?php

namespace Backend\Controllers\User\Product\Upsell;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Product;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Upsell';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/products/upsell/indexView.php';
        $this->user = user();
    }

    public function index(Request $request, $product_id)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $product = Product::where('user_id', $user->id)->where('id', $product_id)->first();
        View::render($this->indexFile, compact('title', 'context', 'user', 'product'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $body = $request->pageParams();
        $product_id = $body->id;
        $product = Product::where('user_id', $user->id)->where('id', $product_id)->first();
        View::response($this->indexFile, compact('title', 'context', 'user', 'product'));
    }
}