<?php

namespace Backend\Controllers\Subdomains\Checkout;

use Backend\App;
use Backend\Http\Request;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Product;
use Backend\Exceptions\Product\ProductNotFoundException;
use Backend\Controllers\Browser\NotFoundController;

class CheckoutController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Checkout';
        $this->context = 'public';
        $this->indexFile = 'frontend/view/subdomains/checkout/checkoutView.php';
        $this->user = user();
        $this->subdomain = 'checkout';
        $this->domain = get_subdomain_serialized($this->subdomain);
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        View::render($this->indexFile, compact('title', 'context', 'user'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        View::response($this->indexFile, compact('title', 'context', 'user'));
    }
}