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
use Backend\Models\Customer;
use Backend\Models\Upsell;
use Backend\Models\ProductLink;
use Backend\Exceptions\Product\ProductNotFoundException;
use Backend\Controllers\Browser\NotFoundController;
use chillerlan\QRCode\QRCode;

class PixController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Checkout';
        $this->context = 'public';
        $this->indexFile = 'frontend/view/subdomains/checkout/pixView.php';
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
        $product_link_id = get_ordermeta($order->id, 'product_link');
        $product_link = ProductLink::find($product_link_id);
        $product = $order->product();
        $checkout_id = $order->checkout_id;
        $checkout = Checkout::where('id', $checkout_id)->first();
        $customer = Customer::find($order?->customer_id);
        $upsell = Upsell::where('user_id', $product->user_id)->where('product_id', $product->id)->first();
        
        View::render($this->indexFile, compact('title', 'context', 'user', 'id', 'order', 'product', 'checkout', 'customer', 'upsell', 'product_link'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $id = $request->query('id');
        $order = Order::where('uuid', $id)->first();
        $product_link_id = get_ordermeta($order->id, 'product_link');
        $product_link = ProductLink::find($product_link_id);
        $product = $order->product();
        $checkout_id = $order->checkout_id;
        $checkout = Checkout::where('id', $order->checkout_id)->first();
        $customer = Customer::find($order?->customer_id);
        $upsell = Upsell::where('user_id', $product->user_id)->where('product_id', $product->id)->first();

        View::response($this->indexFile, compact('title', 'context', 'user', 'id', 'order', 'product', 'checkout', 'customer', 'upsell', 'product_link'));
    }

    public function watch(Request $request)
    {
        $transaction_id = $request->query('transaction_id');
        $order = Order::where('transaction_id', $transaction_id)->first();
        if (empty($order)) Response::json(["status" => "error", "message" => "Transação não encontrada."]);

        $status = $order->status;

        return Response::json(compact('status'));
    }
}