<?php

namespace Backend\Controllers\User\Upsell;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Http\Link;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Upsell;
use Backend\Models\Product;
use \Illuminate\Database\Eloquent\ModelNotFoundException;
use Backend\Controllers\Browser\NotFoundController;
use Backend\Exceptions\Upsell\
{
    UpsellNotFoundException,
    EmptyNameException,
    EmptyStatusException,
    EmptyProductException,
    EmptyPriceException,
    EmptyAcceptRedirectTypeException,
    EmptyRefuseRedirectTypeException,
    EmptyAcceptTextException,
    EmptyRefuseTextException
};

class EditController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Edit Upsell';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/upsells/editView.php';
        $this->user = user();
    }

    public function index(Request $request, $id)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        try
        {
            $upsell = Upsell::where('id', $id)->where('user_id', $user->id)->with(['product' => fn($query) => $query->with('product_links')])->first();
            
            if (empty($upsell)) throw new ModelNotFoundException;

            $products = Product::where('user_id', $user->id)->get();
            $product = $upsell->product ?? null;
            
            View::render($this->indexFile, compact('title', 'context', 'user', 'upsell', 'products', 'product'));
        }

        catch (ModelNotFoundException $ex)
        {
            // nao encontrado ou nao pertence ao usuario logado
            // redirecionar de volta para a listagem
            
            $link = new Link($this->application);
            $link->to(site_url(), '/upsells'); // renderiza o html
            Link::changeUrl(site_url(), '/upsells'); // altera url
        }
    }

    public function element(Request $request)
    {
        $user = $this->user;
        
        $body = $request->pageParams();
        $id = $body?->id;
        $title = $this->title;
        $context = $this->context;

        try
        {
            $upsell = Upsell::where('id', $id)->where('user_id', $user->id)->with(['product' => fn($query) => $query->with('product_links')])->first();
            if (empty($upsell)) throw new ModelNotFoundException;

            $products = Product::where('user_id', $user->id)->get();
            $product = $upsell->product ?? null;

            View::response($this->indexFile, compact('title', 'context', 'user', 'upsell', 'products', 'product'));
        }

        catch (ModelNotFoundException $ex)
        {
            $notfound = new NotFoundController($this->application);
            $notfound->element(new Request);
        }
    }

    public function update(Request $request, $id)
    {
        $user = $this->user;

        $response = [];
        $body = $request->json();
        $name = $body?->name ?? '';
        $status = $body?->status ?? '';
        $product = $body?->product ?? '';
        $price_var = $body?->price_var ?? '';
        $accept_redirect = $body?->accept_redirect ?? '';
        $accept_page = $body?->accept_page ?? '';
        $refuse_redirect = $body?->refuse_redirect ?? '';
        $refuse_page = $body?->refuse_page ?? '';
        $accept_text = $body?->accept_text ?? '';
        $refuse_text = $body?->refuse_text ?? '';

        try
        {
            $upsell = Upsell::where('id', $id)->where('user_id', $user->id)->first();

            if (empty($upsell)) throw new UpsellNotFoundException;
            if (!$name) throw new EmptyNameException;
            if (!$status) throw new EmptyStatusException;
            if (!$product) throw new EmptyProductException;
            if (!$price_var && $price_var <> '0') throw new EmptyPriceException;
            if (!$accept_redirect) throw new EmptyAcceptRedirectTypeException;
            if (!$refuse_redirect) throw new EmptyRefuseRedirectTypeException;
            if (!$accept_text) throw new EmptyAcceptTextException;
            if (!$refuse_text) throw new EmptyRefuseTextException;

            $upsell->name = $name;
            $upsell->status = $status;
            $upsell->product_id = $product;
            if ($price_var <> '0') $upsell->product_link_id = $price_var;
            $upsell->accept_redirect = $accept_redirect;
            $upsell->accept_text = $accept_text;
            $upsell->accept_page = $accept_page;
            $upsell->refuse_redirect = $refuse_redirect;
            $upsell->refuse_text = $refuse_text;
            $upsell->refuse_page = $refuse_page;
            $upsell->save();

            $response = ["status" => "success", "message" => "Upsell atualizado com sucesso."];
        }

        catch(UpsellNotFoundException)
        {
            $response = ["status" => "error", "message" => "Upsell não encontrado."];
        }

        catch(EmptyNameException)
        {
            $response = ["status" => "error", "message" => "O nome não pode ser vazio."];
        }

        catch(EmptyStatusException)
        {
            $response = ["status" => "error", "message" => "O status não pode estar vazio."];
        }

        catch(EmptyProductException)
        {
            $response = ["status" => "error", "message" => "Escolha um produto para este upsell."];
        }

        catch(EmptyPriceException)
        {
            $response = ["status" => "error", "message" => "Selecione o preço do produto no upsell."];
        }

        catch(EmptyAcceptRedirectTypeException)
        {
            $response = ["status" => "error", "message" => "Selecione o tipo de redirecionamento do botão \"aceitar oferta\"."];
        }

        catch(EmptyRefuseRedirectTypeException)
        {
            $response = ["status" => "error", "message" => "Selecione o tipo de redirecionamento do botão \"recusar oferta\"."];
        }

        catch(EmptyAcceptTextException)
        {
            $response = ["status" => "error", "message" => "Preencha o texto do botão \"aceitar\"."];
        }

        catch(EmptyRefuseTextException)
        {
            $response = ["status" => "error", "message" => "Preencha o texto do botão \"recusar\"."];
        }

        finally
        {
            Response::json($response);
        }
    }
}