<?php

namespace Backend\Controllers\User\OrderBump;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Exceptions\Product\OrderbumpNotFoundException;
use Backend\Enums\Orderbump\EOrderbumpStatus;
use Backend\Models\User;
use Backend\Models\Orderbump;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Order Bumps';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/order_bumps/indexView.php';
        $this->user = user();
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        $page = $request->query('page') ?: 1;
        $per_page = 10;

        $url = get_current_route();

        $orderbumps = Orderbump::where('user_id', $user->id)->orderBy('id', 'DESC')
            ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);
        $info = $orderbumps;
        View::render($this->indexFile, compact('title', 'context', 'user', 'orderbumps', 'url', 'info'));
    }

//    public function element(Request $request)
//    {
//        $title = $this->title;
//        $context = $this->context;
//        $user = $this->user;
//
//        $page = $request->query('page') ?: 1;
//        $per_page = 10;
//
//        $url = site_url().'/orderbumps';
//
//        $orderbumps = Orderbump::where('user_id', $user->id)->orderBy('id', 'DESC')
//            ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);
//        $info = $orderbumps;
//        View::response($this->indexFile, compact('title', 'context', 'user', 'orderbumps', 'url', 'info'));
//    }

    public function new(Request $request)
    {
        $user = $this->user;

        $orderbump = new Orderbump;
        $orderbump->user_id = $user->id;
        $orderbump->name = "Rascunho #".time();
        $orderbump->price = 10;
        $orderbump->status = EOrderbumpStatus::DRAFT;
        $orderbump->save();

        Response::json(["message" => "Orderbump criado com sucesso.", "id" => $orderbump->id]);
    }

    public function destroy(Request $request, $id)
    {
        $user = $this->user;
        $response = [];

        try
        {
            $orderbump = Orderbump::where('id', $id)->where('user_id', $user->id)->first();
            if (empty($orderbump)) throw new OrderbumpNotFoundException;

            $orderbump->delete();

            $response = ["status" => "success", "message" => "Orderbump deletado com sucesso."];
        }

        catch(OrderbumpNotFoundException $ex)
        {
            $response = ["status" => "error", "message" => "Orderbump não encontrado."];
        }

        finally
        {
            Response::json($response);
        }
    }
}