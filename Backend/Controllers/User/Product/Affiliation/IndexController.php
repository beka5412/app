<?php

namespace Backend\Controllers\User\Product\Affiliation;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Enums\Product\EProductAffPaymentType;
use Backend\Models\User;
use Backend\Models\Product;
use Backend\Exceptions\Product\ProductNotFoundException;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Pixel';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/products/affiliation/indexView.php';
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

    public function update(Request $request, $product_id)
    {
        $user = $this->user;
        $response = [];
        $body = $request->json();
        $enabled = $body->enabled;
        $marketplace_enabled = $body->marketplace_enabled;
        $aff_payment_type = $body->aff_payment_type;
        $comission = $body->comission;
        $cookie_mode = $body->cookie_mode;
        $cookie_duration = (int) $body->cookie_duration;

        try
        {
            $comission = str_replace('.', '', $comission);
            $comission = (double) str_replace(',', '.', $comission);

            $aff_payment_type = $aff_payment_type == 1 ? EProductAffPaymentType::PERCENT->value : EProductAffPaymentType::PRICE->value;

            $product = Product::where('id', $product_id)->where('user_id', $user->id)->first();
            if (empty($product)) throw new ProductNotFoundException;
            $product->affiliate_enabled = $enabled;
            $product->marketplace_enabled = $marketplace_enabled;
            $product->affiliate_payment_type = $aff_payment_type;
            $product->affiliate_amount = $comission;
            $product->cookie_mode = $cookie_mode;
            $product->cookie_duration = $cookie_duration;
            $product->save();

            $response = ["status" => "success", "message" => "Afiliação atualizada com sucesso."];
        }

        catch(ProductNotFoundException $ex)
        {
            $response = ["status" => "error", "message" => "Produto não encontrado."];
        }

        finally
        {
            Response::json($response);
        }
    }
}