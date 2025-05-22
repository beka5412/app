<?php

namespace Backend\Controllers\User\Sale;

use Backend\App;
use Backend\Enums\Order\EOrderStatus;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Order;

class ShowController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Show Orders';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/sales/showView.php';
        $this->user = user();
    }

    public function index(Request $request, $id)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $order = Order::where('id', $id)->where(fn($query) => $query->where('user_id', $user->id)->orWhere('aff_id', $user->id))->first();
        $ordermeta = $order->metas();
        $orderbumps = $order->orderbumps();
        $aff = User::find($order->aff_id);
        View::render($this->indexFile, compact('title', 'context', 'user', 'order', 'ordermeta', 'orderbumps', 'aff'));
    }

    public function element(Request $request)
    {
        $body = $request->pageParams();
        $id = $body?->id;
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $order = Order::where('id', $id)->where(fn($query) => $query->where('user_id', $user->id)->orWhere('aff_id', $user->id))->first();
        $ordermeta = $order->metas();
        $orderbumps = $order->orderbumps();
        $aff = User::find($order->aff_id);
        View::response($this->indexFile, compact('title', 'context', 'user', 'order', 'ordermeta', 'orderbumps', 'aff'));
    }
}