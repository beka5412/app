<?php

namespace Backend\Controllers\User\Coupon;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Http\Link;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Product;
use Backend\Models\Coupon;
use Backend\Exceptions\Coupon\
{
    EmptyCodeException,
    EmptyDiscountException,
    EmptyDescriptionException,
    EmptyStatusException,
    EmptyTypeException,
    CouponNotFoundException
};
use \Illuminate\Database\Eloquent\ModelNotFoundException;
use Backend\Controllers\Browser\NotFoundController;

class EditController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Edit Coupon';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/coupons/editView.php';
        $this->user = user();
    }

    public function index(Request $request, $id)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        // LEMBRAR:
        // fazer esta verificacao em todos os "Editar", "Deletar", etc..., tudo que envolver a busca do item que 
        // percence a um usuario
        try
        {
            $coupon = Coupon::where('id', $id)->where('user_id', $user->id)->first();
            if (empty($coupon)) throw new ModelNotFoundException;

            View::render($this->indexFile, compact('title', 'context', 'user', 'coupon'));
        }

        catch (ModelNotFoundException $ex)
        {
            // nao encontrado ou nao pertence ao usuario logado
            // redirecionar de volta para a listagem de cupons
            $link = new Link($this->application);
            $link->to(site_url(), '/coupons');
            Link::changeUrl(site_url(), '/coupons');
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
            $coupon = Coupon::where('id', $id)->where('user_id', $user->id)->first();
            if (empty($coupon)) throw new ModelNotFoundException;

            View::response($this->indexFile, compact('title', 'context', 'user', 'coupon'));
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
        $code = $body?->code ?? '';
        $discount = $body?->discount ?? '';
        $description = $body?->description ?? '';
        $status = $body?->status ?? '';
        $type = $body?->type ?? '';

        try
        {
            $coupon = Coupon::where('id', $id)->where('user_id', $user->id)->first();

            if (empty($coupon)) throw new CouponNotFoundException;
            if (!$code) throw new EmptyCodeException;
            if (!$discount) throw new EmptyDiscountException;
            // if (!$description) throw new EmptyDescriptionException;
            if (!$status) throw new EmptyStatusException;
            if (!$type) throw new EmptyTypeException;

            $coupon->code = $code;
            $coupon->discount = $discount;
            $coupon->description = $description;
            $coupon->status = $status;
            $coupon->type = $type;
            $coupon->save();

            $response = ["status" => "success", "message" => "Cupom atualizado com sucesso."];
        }

        catch(CouponNotFoundException $ex)
        {
            $response = ["status" => "error", "message" => "Cupom não encontrado."];
        }

        catch(EmptyCodeException $ex)
        {
            $response = ["status" => "error", "message" => "O código não pode estar em branco."];
        }

        catch(EmptyDiscountException $ex)
        {
            $response = ["status" => "error", "message" => "O valor do desconto não pode estar em branco."];
        }

        // catch(EmptyDescriptionException $ex)
        // {
        //     $response = ["status" => "error", "message" => "O a descrição não pode estar em branco."];
        // }

        catch(EmptyStatusException $ex)
        {
            $response = ["status" => "error", "message" => "O status de visibilidade não pode estar em branco."];
        }

        catch(EmptyTypeException $ex)
        {
            $response = ["status" => "error", "message" => "O tipo de desconto não pode estar em branco."];
        }

        finally
        {
            Response::json($response);
        }
    }
}