<?php

namespace Backend\Controllers\User\OrderBump;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Product;
use Backend\Models\Orderbump;
use Backend\Exceptions\Orderbump\
{
    OrderbumpNotFoundException,
    EmptyNameException,
    EmptyStatusException,
    EmptyProductException,
    EmptyPriceException,
    EmptyTextButtonException,
    EmptyTitleException,
    EmptyDescriptionException,
    EmptyProductAsCheckoutException
};

class EditController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Editar Orderbump';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/order_bumps/editView.php';
        $this->user = user();
    }

    public function index(Request $request, $id)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $products = Product::where('user_id', $user->id)->get();
        $orderbump = Orderbump::where('id', $id)->where('user_id', $user->id)->first();

        View::render($this->indexFile, compact('title', 'context', 'user', 'orderbump', 'products'));

    }

    public function element(Request $request)
    {
        $body = $request->pageParams();
        $id = $body?->id;
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $products = Product::where('user_id', $user->id)->get();
        $orderbump = Orderbump::where('id', $id)->where('user_id', $user->id)->first();

        View::response($this->indexFile, compact('title', 'context', 'user', 'orderbump', 'products'));
    }

    public function update(Request $request, $id)
    {
        $user = $this->user;
        $response = [];
        $body = $request->json();
        $name = $body->name;
        $status = $body->status;
        $product = $body->product;
        $price = $body->price;
        $price_promo = $body->price_promo;
        $text_button = $body->text_button;
        $title = $body->title;
        $description = $body->description;
        $product_as_checkout = $body->product_as_checkout;

        try
        {
            $orderbump = Orderbump::where('id', $id)->where('user_id', $user->id)->first();

            if (empty($orderbump)) throw new OrderbumpNotFoundException;
            if (!$name) throw new EmptyNameException;
            if (!$status) throw new EmptyStatusException;
            if (!$product) throw new EmptyProductException;
            // if (!$price) throw new EmptyPriceException;
            if (!$text_button) throw new EmptyTextButtonException;
            // if (!$title) throw new EmptyTitleException;
            if (!$description) throw new EmptyDescriptionException;
            if (!$product_as_checkout) throw new EmptyProductAsCheckoutException;

            $orderbump->name = $name;
            $orderbump->status = $status;
            $orderbump->product_id = $product;
            $orderbump->price = $price;
            $orderbump->price_promo = $price_promo;
            $orderbump->text_button = $text_button;
            $orderbump->title = $title;
            $orderbump->description = $description;
            $orderbump->product_as_checkout_id = $product_as_checkout;
            $orderbump->save();

            $response = ["status" => "success", "message" => "Orderbump atualizado com sucesso."];
        }

        catch(OrderbumpNotFoundException $ex)
        {
            $response = ["status" => "error", "message" => "Orderbump não encontrado."];
        }

        catch(EmptyNameException $ex)
        {
            $response = ["status" => "error", "message" => "O nome do orderbump não pode estar em branco."];
        }

        catch(EmptyStatusException $ex)
        {
            $response = ["status" => "error", "message" => "O status do orderbump não pode estar em branco."];
        }

        catch(EmptyProductException $ex)
        {
            $response = ["status" => "error", "message" => "O produto usado neste orderbump não pode estar em branco."];
        }

        catch(EmptyPriceException $ex)
        {
            $response = ["status" => "error", "message" => "O preço do orderbump não pode estar zerado."];
        }

        catch(EmptyTextButtonException $ex)
        {
            $response = ["status" => "error", "message" => "O texto do botão no orderbump não pode estar zerado."];
        }

        catch(EmptyTitleException $ex)
        {
            $response = ["status" => "error", "message" => "O título no orderbump não pode estar zerado."];
        }

        catch(EmptyDescriptionException $ex)
        {
            $response = ["status" => "error", "message" => "A descrição no orderbump não pode estar zerado."];
        }

        catch(EmptyProductAsCheckoutException $ex)
        {
            $response = ["status" => "error", "message" => "O produto que representa o checkout neste orderbump não pode estar zerado."];
        }

        finally
        {
            Response::json($response);
        }
    }
}