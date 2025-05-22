<?php 

namespace Backend\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Backend\Models\Category;
use Backend\Models\User;
use Backend\Models\Orderbump;
use Backend\Models\ProductLink;
use Backend\Models\Checkout;
use Backend\Models\Affiliation;

class Product extends Model
{
    public function category() : BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function language() : BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderbumps() : HasMany
    {
        return $this->hasMany(Orderbump::class, 'product_as_checkout_id');
    }

    public function product_links() : HasMany
    {
        return $this->hasMany(ProductLink::class, 'product_id');
    }

    public function defaultCheckout()
    {
        return Checkout::where('product_id', $this->id)->where('default', 1)->first();
    }

    public function checkouts() : HasMany
    {
        return $this->hasMany(Checkout::class);
    }

    public function affiliation() : HasOne
    {
        return $this->hasOne(Affiliation::class, 'product_id');
    }

    public function last_request()
    {
        return ProductRequest::where('product_id', $this->id)->orderBy('id', 'DESC')->first();
    }
}