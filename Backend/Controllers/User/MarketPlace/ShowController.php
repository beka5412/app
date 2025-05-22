<?php

namespace Backend\Controllers\User\MarketPlace;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Product;
use Backend\Models\Affiliation;
use Backend\Exceptions\Product\ProductNotFoundException;

class ShowController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Market place';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/marketplace/showView.php';
        $this->user = user();
    }

    public function index(Request $request, $product_id)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        $affiliated = Affiliation::where('user_id', $user->id)->where('product_id', $product_id)->first();
        $product = Product::where('id', $product_id)->first();

        View::render($this->indexFile, compact('title', 'context', 'user', 'product', 'affiliated'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        $body = $request->pageParams();
        $product_id = $body?->id;

        $product = Product::where('id', $product_id)->first();
        $affiliated = Affiliation::where('user_id', $user->id)->where('product_id', $product_id)->first();

        View::response($this->indexFile, compact('title', 'context', 'user', 'product', 'affiliated'));
    }

    public function promote(Request $request, $product_id)
    {
        $user_id = $this->user->id;

        $affiliation = Affiliation::where('user_id', $user_id)->where('product_id', $product_id)->first();

        if (empty($affiliation))
        {
            $affiliation = new Affiliation;
            $affiliation->user_id = $user_id;
        }

        $affiliation->product_id = $product_id;
        $affiliation->save();
    }

    public function demote(Request $request, $product_id)
    {
        $user_id = $this->user->id;

        Affiliation::where('user_id', $user_id)->where('product_id', $product_id)->delete();
    }
}