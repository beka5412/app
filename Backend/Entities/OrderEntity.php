<?php

namespace Backend\Entities;

use Backend\Models\Order;
use Backend\Models\OrderMeta;
use Backend\Models\Product;
use Backend\Models\User;

class OrderEntity
{
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Lista os produtos do pedido
     *
     * @return object<array>
     */
    public function getProducts() : object
    {
        $order = $this->order;

        // busca todos os dados dos produtos comprados no checkout (apenas os produtos base, ou seja, que nao sao adicionais como um orderbump)
        $products = [];
        $products_base = [];
        $product_names = [];
        $meta_products = OrderMeta::where('order_id', $order->id)->where('name', 'product_id')->get();
        foreach ($meta_products as $meta_product)
        {
            $product = Product::find($meta_product->value);
            if (!empty($product))
            {
                $products_base[] = $product;
                $product_names[] = $product->name;
                $products[] = $product;
            }
        }

        // lista os produtos em orderbump e adiciona na lista completa de produtos comprados
        $meta_orderbumps = OrderMeta::where('order_id', $order->id)->where('name', 'orderbump_items')->first();
        $meta_orderbumps = json_decode($meta_orderbumps->value ?? '[]');
        foreach ($meta_orderbumps as $orderbump)
        {
            $product = Product::find($orderbump->product_id);
            if (!empty($product)) 
            {
                $product_names[] = $product->name;
                $products[] = $product;
            }
        }

        return (Object) [
            "all_products" => $products,
            "base_products" => $products_base,
            "product_names" => $product_names
        ];
    }

    /**
     * Recupera o dono do checkout
     *
     * @return User
     */
    public function getUser() : User
    {
        return User::find($this->order->user_id);
    }
}