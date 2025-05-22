<?php

namespace Backend\Models;

use Illuminate\Database\Eloquent\Model;
use Backend\Models\Order;
use Backend\Models\Customer;
use Backend\Models\OrderMeta;
use Backend\Models\Product;

class Recurrence extends Model
{
    // Definindo a tabela associada ao modelo (caso o nome seja diferente de "recurrences")
    protected $table = 'subscriptions';

    // Definindo os campos que podem ser preenchidos via mass assignment
    protected $fillable = ['order_id', 'status', 'start_date', 'end_date'];

    // Relação com a tabela orders
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }    

    // Relação com a tabela customers através de order
    public function customer()
    {
        return $this->hasOneThrough(Customer::class, Order::class, 'id', 'id', 'order_id', 'customer_id');
    }

    // Relação com a tabela order_meta
    public function orderMeta()
    {
        return $this->hasMany(OrderMeta::class, 'order_id', 'order_id')
                    ->where('name', 'product_id');
    }

    // Relação com a tabela products
    public function product()
    {
        return $this->hasOneThrough(Product::class, OrderMeta::class, 'order_id', 'id', 'order_id', 'value');
    }
}
