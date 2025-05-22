<?php

namespace Backend\Controllers\Api;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Enums\Order\EOrderStatus;
use Backend\Models\User;
use Backend\Models\Order;
use Backend\Models\Balance;

class PaySellerController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
    }

    public function main(Request $request)
    {
        $now = today();


        /**
         * Seller
         */

        // buscar os pedidos de com data para credito sendo hoje
        $orders = Order::where('queue_seller_credit', 1) // se esta na fila
            ->where('seller_credited_at', '<=', $now) // se chegou/passou a data
            ->where('status', EOrderStatus::APPROVED->value) // se aprovada
            ->get();

        foreach ($orders as $order)
        {
            $balance = Balance::where('user_id', $order->user_id)->first();
            $balance->available = doubleval($balance->available) + doubleval($order->total_seller);
            $balance->future_releases = doubleval($balance->future_releases) - $order->total_seller;
            $balance->save();

            $order->queue_seller_credit = 0;
            $order->seller_was_credited = 1;
            $order->save();
        }

        
        /**
         * Affiliate
         */

        // // buscar os pedidos de com data para credito sendo hoje
        // $orders = Order::where('queue_aff_credit', 1) // se esta na fila
        //     ->where('aff_credited_at', '<=', $now) // se chegou/passou a data
        //     ->where('status', EOrderStatus::APPROVED->value) // se aprovada
        //     ->get();

        // foreach ($orders as $order)
        // {
        //     $balance = Balance::where('user_id', $order->aff_id)->first();
        //     $balance->available = doubleval($balance->available) + doubleval($order->total_aff);
        //     $balance->future_releases = doubleval($balance->future_releases) - $order->total_aff;
        //     $balance->save();

        //     $order->queue_aff_credit = 0;
        //     $order->aff_was_credited = 1;
        //     $order->save();
        // }
    }
}