<?php

namespace Backend\Controllers\User\AbandonedCart;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Product;
use Backend\Models\AbandonedCart;
use Backend\Exceptions\Product\ProductNotFoundException;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Abandoned Carts';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/abandoned_carts/indexView.php';
        $this->user = user();
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        $page = $request->query('page') ?: 1;
        $per_page = 10;
        $url = get_current_route();

        $abandoned_carts = AbandonedCart::where('user_id', $user->id)->orderBy('id', 'DESC')->paginate(10);
        $info = $abandoned_carts;

        View::render($this->indexFile, compact('title', 'context', 'user', 'abandoned_carts', 'info', 'url'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        
        $page = $request->query('page') ?: 1;
        $per_page = 10;
        $url = site_url().'/abandoned-carts';

        $abandoned_carts = AbandonedCart::where('user_id', $user->id)->orderBy('id', 'DESC')->paginate(10);
        $info = $abandoned_carts;

        View::response($this->indexFile, compact('title', 'context', 'user', 'abandoned_carts', 'info', 'url'));
    }

    public function full(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $products = Product::where('user_id', $user->id)->orderBy('id', 'DESC')->paginate(10);
        View::render($this->indexFile, compact('title', 'context', 'user', 'products'), ['no_js' => true]);
    }
}