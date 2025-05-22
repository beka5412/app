<?php 

namespace Backend\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Backend\Models\Product;

class Bestseller extends Model
{
    public function product() : BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}