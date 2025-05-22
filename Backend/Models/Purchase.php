<?php 

namespace Backend\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Backend\Models\Product;
use Backend\Models\Customer;
use Backend\Models\Order;

class Purchase extends Model
{
    public function product() : BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    public function customer() : BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order() : BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function refund_request() : HasOne
    {
        return $this->hasOne(RefundRequest::class);
    }
}