<?php

namespace Backend\Controllers\Admin\Order;

use Backend\App;
use Backend\Http\Request;
use Backend\Template\View;
use Backend\Models\Administrator;
use Backend\Models\User;
use Backend\Models\Order;
class ShowController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Order';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/admin/orders/showView.php';
        $this->admin = admin();
        
    }

    public function index(Request $request, $id)
    {
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;
        $order = Order::where('id', $id)->with('customer')->first();
        $ordermeta = $order->metas();
        $orderbumps = $order->orderbumps();
        $aff = User::find($order->aff_id);
        View::render($this->indexFile, compact('title', 'context', 'admin','order', 'ordermeta', 'orderbumps', 'aff'));
    }

    public function element(Request $request)
    {
        
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;
        $order = Order::where('id', $id)->with('customer')->first();
        $ordermeta = $order->metas();
        $orderbumps = $order->orderbumps();
        $aff = User::find($order->aff_id);
        View::render($this->indexFile, compact('title', 'context', 'admin','order', 'ordermeta', 'orderbumps', 'aff'));
    }
}