<?php

namespace Backend\Controllers\User\Product\Pixel;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Product;
use Backend\Models\Pixel;
use Backend\Models\Domain;
// use Backend\Exceptions\Customer\EmptyNameException;
// use Backend\Exceptions\Customer\EmptyEmailException;
// use Backend\Exceptions\Customer\CustomerNotFoundException;

class EditController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Editar pixel';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/products/pixels/editView.php';
        $this->user = user();
    }

    public function index(Request $request, $product_id, $pixel_id)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $domains = Domain::where('user_id', $user->id)->get();
        $product = Product::where('user_id', $user->id)->where('id', $product_id)->first();
        $pixel = Pixel::where('id', $pixel_id)->where('user_id', $user->id)->first();

        View::render($this->indexFile, compact('title', 'context', 'user', 'pixel', 'product', 'domains'));
    }

    public function element(Request $request)
    {
        $body = $request->pageParams();
        $product_id = $body?->product_id;
        $pixel_id = $body?->id;
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $domains = Domain::where('user_id', $user->id)->get();
        $product = Product::where('user_id', $user->id)->where('id', $product_id)->first();
        $pixel = Pixel::where('id', $pixel_id)->where('user_id', $user->id)->first();

        View::response($this->indexFile, compact('title', 'context', 'user', 'pixel', 'product', 'domains'));
    }

    public function update(Request $request, $product_id, $id)
    {
        $user = $this->user;
        $response = [];
        $body = $request->json();
        $name = $body->name;
        $platform = $body->platform;
        $content = $body->content;
        $access_token = $body->access_token;
        $domain = $body->domain;
        $metatag = $body->metatag;
        if (!str_contains($metatag, "<meta")) $metatag = '';

        try
        {
            $pixel = Pixel::where('id', $id)->where('user_id', $user->id)->first();

            // if (empty($customer)) throw new CustomerNotFoundException;
            // if (!$name) throw new EmptyNameException;
            // if (!$email) throw new EmptyEmailException;

            $pixel->name = $name;
            $pixel->platform = $platform;
            $pixel->content = $content;
            $pixel->access_token = $access_token;
            $pixel->metatag = $metatag;
            if ($domain) $pixel->domain_id = $domain;
            $pixel->save();

            $response = ["status" => "success", "message" => "Pixel atualizado com sucesso."];
        }

        // catch(CustomerNotFoundException $ex)
        // {
        //     $response = ["status" => "error", "message" => "Cliente não encontrado."];
        // }

        // catch(EmptyNameException $ex)
        // {
        //     $response = ["status" => "error", "message" => "O nome do cliente não pode estar em branco."];
        // }

        // catch(EmptyEmailException $ex)
        // {
        //     $response = ["status" => "error", "message" => "O e-mail do cliente não pode estar em branco."];
        // }

        finally
        {
            Response::json($response);
        }
    }
}