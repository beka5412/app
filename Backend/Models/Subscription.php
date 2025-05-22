<?php 

namespace Backend\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Backend\Models\Customer;
use Backend\Models\Order;

class Subscription extends Model
{
    public function customer() : BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order() : BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    
        protected $table = 'subscriptions'; // Definindo a tabela correspondente

}