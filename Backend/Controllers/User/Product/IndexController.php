<?php

namespace Backend\Controllers\User\Product;

use Backend\App;
use Backend\Controllers\Browser\Dashboard\NotFoundController;
use Backend\Controllers\Controller\AFrontendController;
use Backend\Controllers\Controller\TFrontendController;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Product;
use Backend\Models\Checkout;
use Backend\Exceptions\Product\ProductNotFoundException;
use Backend\Enums\Checkout\ECheckoutStatus;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class IndexController extends AFrontendController
{
    use TFrontendController;
    public string $title = 'Products';
    public string $context = 'dashboard';
    public string $indexFile = 'frontend/view/user/products/indexView.php';

    public function view(string $view_method, Request $request, array $params=[], array $pagination=[])
    {
        extract((array) $this);
        extract($params);
        extract($pagination);
        
        $url = site_url().'/products';

        $products = Product::where('user_id', $user->id)->orderBy('id', 'DESC')
            ->paginate(perPage: 12, columns: ['*'], pageName: 'page', page: $page);

        $info = $products;
        
        return View::$view_method($this->indexFile, compact('title', 'context', 'user', 'products', 'info', 'url'));
    }

    public function destroy(Request $request, $id)
    {
        $user = $this->user;
        $response = [];

        try
        {
            $product = Product::where('id', $id)->where('user_id', $user->id)->first();
            if (empty($product)) throw new ProductNotFoundException;

            $product->delete();

            $response = ["status" => "success", "message" => "Produto deletado com sucesso."];
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

    public function new(Request $request)
    {
        $user = $this->user;

        $body = $request->json();

        $product_name = $body->product_name;
        $payment_type = $body->payment_type;
        $product_type = $body->product_type;

        $product = new Product;
        $product->user_id = $user->id;
        $product->name = $product_name ?: "Rascunho #".time();
        $product->price = 49;
        $product->publish_status = 'draft';
        $product->sku = strtoupper(uniqid());
        $product->credit_card_enabled = 1;
        $product->pix_enabled = 1;
        $product->billet_enabled = 1;
        $product->payment_type = $payment_type;
        $product->type = $product_type;
        $product->save();

        $checkout = new Checkout;
        $checkout->product_id = $product->id;
        $checkout->user_id = $user->id;
        $checkout->sku = strtoupper(uniqid());
        $checkout->name = "Checkout Inicial";
        $checkout->status = ECheckoutStatus::PUBLISHED;
        $checkout->checkout_theme_id = 1;
        $checkout->default = 1;
        $checkout->pix_enabled = 1;
        $checkout->credit_card_enabled = 1;
        $checkout->billet_enabled = 1;
        $checkout->max_installments = 12;
        $checkout->save();

        Response::json(["message" => "Produto criado com sucesso.", "id" => $product->id]);
    }
}