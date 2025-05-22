<?php

namespace Backend\Controllers\Subdomains\Checkout;

use Backend\App;
use Backend\Exceptions\Coupon\CouponNotFoundException;
use Backend\Enums\Lib\Session;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Coupon;

class CouponController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Checkout';
        $this->context = 'public';
        $this->user = user();
        $this->subdomain = 'checkout';
        $this->domain = get_subdomain_serialized($this->subdomain);
    }

    public function main(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        $body = $request->json();
        $coupon = $body->coupon ?? '';

        $response = [];

        try 
        {
            $coupon = Coupon::where('code', $coupon)
            // ->where('product_id', $product_id)
            ->first();
    
            if (empty($coupon)) throw new CouponNotFoundException;

            put_session(Session::CHECKOUT, 'coupon_applied_id', $coupon->id);
    
            $response = [
                "status" => "success",
                "message" => "Cupom aplicado com sucesso.",
                "data" => [
                    "type" => $coupon?->type,
                    "discount" => $coupon?->discount
                ]
            ];
        }

        catch (CouponNotFoundException $ex)
        {
            $response = ["status" => "error", "message" => "Cupom inválido."];
        }

        finally 
        {
            Response::json($response);
        }
    }
}