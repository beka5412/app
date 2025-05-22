<?php

namespace Backend\Controllers\User\Product\Affiliation\Product\Link;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Product;
use Backend\Models\Affiliation;
use Backend\Models\Checkout;
use Backend\Exceptions\Product\ProductNotFoundException;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Links';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/products/affiliation/products/links/indexView.php';
        $this->user = user();
    }

    public function index(Request $request, $product_id)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        $product = Product::with('checkouts')->with('product_links')->where('id', $product_id)
        ->whereHas('affiliation', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->first();
        
        $default_checkout = Checkout::where('product_id', $product->id)->where('default', 1)->orderBy('id', 'DESC')->first();

        View::render($this->indexFile, compact('title', 'context', 'user', 'product', 'default_checkout'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        $body = $request->pageParams();
        $product_id = $body?->id;
        
        $product = Product::with('checkouts')->with('product_links')->where('id', $product_id)
        ->whereHas('affiliation', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->first();

        $default_checkout = Checkout::where('product_id', $product->id)->where('default', 1)->orderBy('id', 'DESC')->first();

        View::response($this->indexFile, compact('title', 'context', 'user', 'product', 'default_checkout'));
    }
}