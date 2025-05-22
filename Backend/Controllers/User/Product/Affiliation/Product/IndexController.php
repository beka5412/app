<?php

namespace Backend\Controllers\User\Product\Affiliation\Product;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Product;
use Backend\Models\Affiliation;
use Backend\Exceptions\Product\ProductNotFoundException;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'My affiliation';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/products/affiliation/products/indexView.php';
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

        $products = Product::with('affiliation')
        ->whereHas('affiliation', function($query) use($user) {
            $query->where('user_id', $user->id);
        })
        ->orderBy('id', 'DESC')
        ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);

        $info = $products;
        
        View::render($this->indexFile, compact('title', 'context', 'user', 'products', 'info', 'url'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        $page = $request->query('page') ?: 1;
        $per_page = 10;
        $url = site_url().'/products';

        $products = Product::with('affiliation')
        ->whereHas('affiliation', function($query) use($user) {
            $query->where('user_id', $user->id);
        })
        ->orderBy('id', 'DESC')
        ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);

        $info = $products;
        
        View::response($this->indexFile, compact('title', 'context', 'user', 'products', 'info', 'url'));
    }
}