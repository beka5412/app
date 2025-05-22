<?php

namespace Backend\Controllers\User\Product\Pixel;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Pixel;
use Backend\Models\Product;
use Backend\Exceptions\Pixel\PixelNotFoundException;
// use Backend\Enums\Checkout\ECheckoutStatus;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Pixel';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/products/pixels/indexView.php';
        $this->user = user();
    }

    public function index(Request $request, $product_id)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $product = Product::where('user_id', $user->id)->where('id', $product_id)->first();
        $pixels = Pixel::where('user_id', $user->id)->where('product_id', $product_id)->orderBy('id', 'DESC')->paginate(10);
        View::render($this->indexFile, compact('title', 'context', 'user', 'pixels', 'product'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $body = $request->pageParams();
        $product_id = $body->id;
        $product = Product::where('user_id', $user->id)->where('id', $product_id)->first();
        $pixels = Pixel::where('user_id', $user->id)->where('product_id', $product_id)->orderBy('id', 'DESC')->paginate(10);
        View::response($this->indexFile, compact('title', 'context', 'user', 'pixels', 'product'));
    }

    public function destroy(Request $request, $id)
    {
        $user = $this->user;
        $response = [];

        try
        {
            $pixel = Pixel::where('id', $id)->where('user_id', $user->id)->first();
            if (empty($pixel)) throw new PixelNotFoundException;

            $pixel->delete();

            $response = ["status" => "success", "message" => "Pixel deletado com sucesso."];
        }

        catch(PixelNotFoundException $ex)
        {
            $response = ["status" => "error", "message" => "Pixel não encontrado."];
        }

        finally
        {
            Response::json($response);
        }
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