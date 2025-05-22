<?php

namespace Backend\Controllers\User\Product\Tool;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Product;
// use Backend\Exceptions\Checkout\CheckoutNotFoundException;
// use Backend\Enums\Checkout\ECheckoutStatus;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Pixel';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/products/tools/indexView.php';
        $this->user = user();
    }

    public function index(Request $request, $product_id)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $product = Product::where('user_id', $user->id)->where('id', $product_id)->first();
        View::render($this->indexFile, compact('title', 'context', 'user', 'product'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $body = $request->pageParams();
        $product_id = $body->id;
        $product = Product::where('user_id', $user->id)->where('id', $product_id)->first();
        View::response($this->indexFile, compact('title', 'context', 'user', 'product'));
    }

    public function destroy(Request $request, $product_id, $checkout_id)
    {
        // $user = $this->user;
        // $response = [];

        // try
        // {
        //     $checkout = Checkout::where('id', $checkout_id)->where('user_id', $user->id)->first();
        //     if (empty($checkout)) throw new CheckoutNotFoundException;

        //     $checkout->delete();

        //     $response = ["status" => "success", "message" => "Checkout deletado com sucesso."];
        // }

        // catch(CheckoutNotFoundException $ex)
        // {
        //     $response = ["status" => "error", "message" => "Checkout não encontrado."];
        // }

        // finally
        // {
        //     Response::json($response);
        // }
    }

    public function new(Request $request, $product_id)
    {
        $user = $this->user;

        $pixel = new Pixel;
        $pixel->product_id = $product_id;
        $pixel->user_id = $user->id;
        // $pixel->sku = strtoupper(uniqid());
        $pixel->name = "Draft #".time();
        // $pixel->status = ECheckoutStatus::DRAFT;
        $pixel->save();

        Response::json(["message" => "Pixel criado com sucesso.", "id" => $pixel->id]);
    }
}