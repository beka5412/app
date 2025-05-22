<?php

namespace Backend\Controllers\User\Product\Setting;

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
        $this->title = 'Produtos';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/products/settings/indexView.php';
        $this->user = user();
    }

    public function index(Request $request, $id)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $product = Product::where('id', $id)->where('user_id', $user->id)->with('category')->first();

        View::render($this->indexFile, compact('title', 'context', 'user', 'product'));
    }

    public function element(Request $request)
    {
        $body = $request->pageParams();
        $id = $body?->id;
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $product = Product::where('id', $id)->where('user_id', $user->id)->with('category')->first();

        View::response($this->indexFile, compact('title', 'context', 'user', 'product'));
    }
}