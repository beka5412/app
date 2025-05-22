<?php 

namespace Backend\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Backend\Models\User;
use Backend\Models\Product;

class Orderbump extends Model
{
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product() : BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function product_as_checkout() : BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_as_checkout_id');
    }
}