<?php

namespace Backend\Controllers\User\Product\Upsell;

use Backend\App;
use Backend\Exceptions\Product\ProductNotFoundException;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Product;

class EditController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Upsell';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/products/upsell/indexView.php';
        $this->user = user();
    }

    public function update(Request $request, Mixed $id)
    {
        $user = $this->user;
        
        $body = $request->json();
        
        $has_upsell = $body->has_upsell ?? 0;
        $upsell_link = $body->upsell_link ?? '';
        $has_upsell_rejection = $body->has_upsell_rejection ?? 0;
        $upsell_text = $body->upsell_text ?? '';
        $upsell_rejection_link = $body->upsell_rejection_link ?? '';
        $upsell_rejection_text = $body->upsell_rejection_text ?? '';

        $response = [];

        try
        {
            $product = Product::where('id', $id)->where('user_id', $user->id)->first();
            if (empty($product)) throw new ProductNotFoundException;

            $product->has_upsell = $has_upsell;
            $product->upsell_link = $upsell_link;
            $product->has_upsell_rejection = $has_upsell_rejection;
            $product->upsell_text = $upsell_text;
            $product->upsell_rejection_link = $upsell_rejection_link;
            $product->upsell_rejection_text = $upsell_rejection_text;
            $product->save();

            $response["status"] = "success.";
            $response["message"] = "Upsell atualizado com sucesso.";
        }

        catch(ProductNotFoundException $ex)
        {
            $response["status"] = "error.";
            $response["message"] = "Produto não encontrado.";
        }

        finally
        {
            Response::json($response);
        }
    }
}