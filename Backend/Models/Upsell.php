<?php 

namespace Backend\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Backend\Models\Product;
use Backend\Models\ProductLink;

class Upsell extends Model
{
    public function product_link() : BelongsTo
    {
        return $this->belongsTo(ProductLink::class);
    }

    public function product() : BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}