<?php

namespace Backend\Controllers\Subdomains\Purchase\Home;

use Backend\App;
use Backend\Http\Request;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Product;
use Backend\Controllers\Browser\NotFoundController;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Dashboard';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/subdomains/purchase/home/indexView.php';
        $this->subdomain = 'purchase';
        $this->domain = get_subdomain_serialized($this->subdomain);
        // $this->user = user();
    }

    public function index(Request $request)
    {
        // $sku = substr($request->uri(), 1);

        $title = $this->title;
        $context = $this->context;
        // $user = $this->user;

        // $product = Product::where('sku', $sku)->first();
        // if (empty($product)) throw new ProductNotFoundException;
        View::render($this->indexFile, compact('title', 'context'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        // $user = $this->user;

        $params = $request->pageParams();
        // $sku = $params->sku;

        // $product = Product::where('sku', $sku)->first();
        // if (empty($product)) throw new ProductNotFoundException;
        View::response($this->indexFile, compact('title', 'context'));
    }
}