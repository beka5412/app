<?php

namespace Backend\Entities;

use Backend\Models\Bestseller;

class BestsellerEntity
{
    public function __construct(Bestseller $best_seller)
    {
        $this->best_seller = $best_seller;
    }

    /**
     * Informa ao banco de dados que o produto teve mais uma venda
     *
     * @param integer|null $product_id
     * @return void
     */
    public static function increment(?int $product_id) : void
    {
        if (empty($product)) return;

        $bestseller = Bestseller::where('product_id', $product_id)->first();
        
        if (empty($bestseller))
        {
            $bestseller = new Bestseller;
            $bestseller->product_id = $product_id;
        }

        $bestseller->sales = intval($bestseller->sales) + 1;
        $bestseller->save();
    }
}