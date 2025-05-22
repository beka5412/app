<?php

namespace Backend\Controllers\Subdomains\Checkout;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Product;
use Backend\Models\Order;
use Backend\Models\Checkout;
use Backend\Exceptions\Product\ProductNotFoundException;
use Backend\Controllers\Browser\NotFoundController;

class PixPaidController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Checkout';
        $this->context = 'public';
        $this->indexFile = 'frontend/view/subdomains/checkout/pixPaidView.php';
        $this->user = user();
        $this->subdomain = 'checkout';
        $this->domain = get_subdomain_serialized($this->subdomain);
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $id = $request->query('id');
        $order = Order::where('uuid', $id)->first();
        $product = $order->product();
        $checkout = Checkout::where('id', $order->checkout_id)->first();
        View::render($this->indexFile, compact('title', 'context', 'user', 'id', 'order', 'product', 'checkout'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $id = $request->query('id');
        $order = Order::where('uuid', $id)->first();
        $product = $order->product();
        $checkout = Checkout::where('id', $order->checkout_id)->first();
        View::response($this->indexFile, compact('title', 'context', 'user', 'id', 'order', 'product', 'checkout'));
    }
}