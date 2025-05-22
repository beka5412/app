<?php

namespace Backend\Controllers\User\Product\Checkout;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Checkout;
use Backend\Models\Product;
use Backend\Exceptions\Checkout\CheckoutNotFoundException;
use Backend\Enums\Checkout\ECheckoutStatus;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Checkout';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/products/checkouts/indexView.php';
        $this->user = user();
    }

    public function index(Request $request, $product_id)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $product = Product::where('user_id', $user->id)->where('id', $product_id)->first();
        $checkouts = Checkout::where('product_id', $product_id)->where('user_id', $user->id)->orderBy('id', 'DESC')->paginate(10);
        View::render($this->indexFile, compact('title', 'context', 'user', 'checkouts', 'product'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $body = $request->pageParams();
        $product_id = $body->id;
        $product = Product::where('user_id', $user->id)->where('id', $product_id)->first();
        $checkouts = Checkout::where('product_id', $product_id)->where('user_id', $user->id)->orderBy('id', 'DESC')->paginate(10);
        View::response($this->indexFile, compact('title', 'context', 'user', 'checkouts', 'product'));
    }

    public function destroy(Request $request, $product_id, $checkout_id)
    {
        $user = $this->user;
        $response = [];

        try
        {
            $checkout = Checkout::where('id', $checkout_id)->where('user_id', $user->id)->first();
            if (empty($checkout)) throw new CheckoutNotFoundException;

            $checkout->delete();

            $response = ["status" => "success", "message" => "Checkout deletado com sucesso."];
        }

        catch(CheckoutNotFoundException $ex)
        {
            $response = ["status" => "error", "message" => "Checkout não encontrado."];
        }

        finally
        {
            Response::json($response);
        }
    }

    public function new(Request $request, $product_id)
    {
        $user = $this->user;

        $checkout = new Checkout;
        $checkout->product_id = $product_id;
        $checkout->user_id = $user->id;
        $checkout->sku = strtoupper(uniqid());
        $checkout->name = "Draft #".time();
        $checkout->status = ECheckoutStatus::PUBLISHED;
        $checkout->pix_enabled = 1;
        $checkout->credit_card_enabled = 1;
        $checkout->billet_enabled = 1;
        $checkout->max_installments = 12;
        $checkout->save();

        Response::json(["message" => "Checkout criado com sucesso.", "id" => $checkout->id]);
    }
}