<?php

namespace Backend\Controllers\Subdomains\Checkout;

use Backend\App;
use Backend\Http\Request;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Product;
use Backend\Models\Order;
use Backend\Models\Checkout;
use Backend\Models\Customer;
use Backend\Exceptions\Product\ProductNotFoundException;
use Backend\Controllers\Browser\NotFoundController;

class BilletController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Checkout';
        $this->context = 'public';
        $this->indexFile = 'frontend/view/subdomains/checkout/billetView.php';
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

        // TODO: implementar 404 caso nao encontre o pedido ou checkout

        $checkout = Checkout::where('id', $order?->checkout_id ?? 0)->first();
        $customer = Customer::find($order?->customer_id);

        View::render($this->indexFile, compact('title', 'context', 'user', 'id', 'order', 'checkout', 'customer'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $id = $request->query('id');
        $order = Order::where('uuid', $id)->first();
        $checkout = Checkout::where('id', $order?->checkout_id ?? 0)->first();
        $customer = Customer::find($order?->customer_id);
        
        View::response($this->indexFile, compact('title', 'context', 'user', 'id', 'order', 'checkout', 'customer'));
    }
}