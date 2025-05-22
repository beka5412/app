<?php 

namespace Backend\Models;

use Illuminate\Database\Eloquent\Model;
use Backend\Models\Order;

class OrderMeta extends Model
{
    public $table = 'order_meta';
    
    public function order() : BelongsTo
    {
        $this->belongsTo(Order::class);
    }
}