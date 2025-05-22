<?php 

namespace Backend\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Backend\Models\OrderMeta;
use Backend\Models\Orderbump;
use Backend\Models\Product;
use Backend\Models\Customer;
use Backend\Models\User;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    public function meta($name)
    {
        $ordermeta = OrderMeta::where('order_id', $this->id)->where('name', $name)->first();
        return $ordermeta->value ?? '';
    }

    public function new_meta() : HasMany
    {
        return $this->hasMany(OrderMeta::class, 'order_id', 'id');
    }

    

    public function product() : ?Product
    {
        return Product::find($this->meta('product_id'));
    }

    public function user() : ?User{
        return User::find($this->user_id);
    }

    public function get_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function metas() 
    {
        $metas = OrderMeta::where('order_id', $this->id)->get();
        $data = [];
        foreach ($metas as $meta) $data[$meta->name] = $meta->value;
        return json_decode(json_encode($data));
    }

    public function orderbumps()
    {
        $orderbumps = [];
        $orderbumps_meta = OrderMeta::where('order_id', $this->id)->where('name', 'orderbump_items')->first();
        foreach (json_decode($orderbumps_meta->value ?? '[]') as $orderbump_meta)
        {
            $orderbumps[] = (Object) [
                "meta" => $orderbump_meta,
                "info" => Orderbump::where('id', $orderbump_meta->id)->with('product')->first()
            ];
        }
        return $orderbumps;
    }

    public function customer() : BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function products()
    {
        return purchased_products($this);
    }

    public function stripe_payment_intent(): HasOne
    {
        return $this->hasOne(StripePaymentIntent::class, 'order_id');
    }
}