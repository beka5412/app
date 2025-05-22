<?php

namespace Backend\Controllers\User\MarketPlace;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Product;
use Backend\Models\Bestseller;
use Backend\Exceptions\Product\ProductNotFoundException;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Market place';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/marketplace/indexView.php';
        $this->user = user();
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $products_on_screen = [];
        $bestsellers = Bestseller::with(['product' => function($query) { $query->where('marketplace_enabled', 1); }])->orderBy('sales', 'DESC')->paginate(12);
        foreach ($bestsellers as $bestseller) if ($product_id = $bestseller?->product?->id) $products_on_screen[] = $product_id;
        $last_products = Product::where('marketplace_enabled', 1)->whereNotIn('id', $products_on_screen)->orderBy('id', 'DESC')->paginate(12);
        View::render($this->indexFile, compact('title', 'context', 'user', 'last_products', 'bestsellers'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $products_on_screen = [];
        $bestsellers = Bestseller::with(['product' => function($query) { $query->where('marketplace_enabled', 1); }])->orderBy('sales', 'DESC')->paginate(12);
        foreach ($bestsellers as $bestseller) if ($product_id = $bestseller?->product?->id) $products_on_screen[] = $product_id;
        $last_products = Product::where('marketplace_enabled', 1)->whereNotIn('id', $products_on_screen)->orderBy('id', 'DESC')->paginate(12);
        View::response($this->indexFile, compact('title', 'context', 'user', 'last_products', 'bestsellers'));
    }

    
}