<?php

namespace Backend\Controllers\Subdomains\Checkout;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Exceptions\Product\ProductNotFoundException;
use Backend\Controllers\Browser\NotFoundController;
use Backend\Models\User;
use Backend\Models\Product;
use Backend\Models\Order;
use Backend\Models\AbandonedCart;
use Backend\Models\Checkout;

class AbandonedCartController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->user = user();
        $this->subdomain = 'checkout';
        $this->domain = get_subdomain_serialized($this->subdomain);
    }

    public function main(Request $request)
    {
        $body = $request->json();
        extract((array) $body);

        $email = trim(strtolower($email));
        $phone = preg_replace("/\D/", "", $phone);

        $abandoned_cart = AbandonedCart::where('email', $email)->first();

        if (empty($abandoned_cart))
        {
            $abandoned_cart = new AbandonedCart;
            $abandoned_cart->email = $email;
        }

        if ($checkout_id) $abandoned_cart->checkout_id = $checkout_id;
        if ($name) $abandoned_cart->name = $name;
        if ($phone) $abandoned_cart->phone = $phone;
        if ($doc) $abandoned_cart->doc = $doc;

        $u = base64_encode(json_encode([
            "name" => $name,
            "email" => $email,
            "phone" => $phone,
            "doc" => $doc
        ]));
        
        $p = "f";

        if (!query_string($url, $p)) $url = "$url?$p=$u";
        $url = query_string_replace($url, $p, $u);

        if ($url) $abandoned_cart->url = $url;

        $abandoned_cart->is_abandoned = 0;
        $abandoned_cart->last_update = today();

        $checkout = Checkout::find($checkout_id);
        $user_id = $checkout->user_id;
        
        $abandoned_cart->user_id = $user_id;
        $abandoned_cart->save();

        Response::json(["status" => "success"]);
    }

    public function alive(Request $request)
    {
        $body = $request->json();
        extract((array) $body);

        $email = trim(strtolower($email));
        
        $abandoned_cart = AbandonedCart::where('email', $email)->first();
        if (!empty($abandoned_cart))
        {
            $abandoned_cart->last_update = today();
            $abandoned_cart->save();
        }
    }
}