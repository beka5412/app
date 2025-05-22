<?php

namespace Backend\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Backend\Models\Product;
use Backend\Models\CheckoutTheme;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Checkout extends Model
{
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function theme(): BelongsTo
    {
        return $this->belongsTo(CheckoutTheme::class, 'checkout_theme_id');
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }
}
