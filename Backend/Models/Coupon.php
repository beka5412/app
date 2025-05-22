<?php 

namespace Backend\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Backend\Models\User;

class Coupon extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'code',
        'discount',
        'description',
        'status',
        'type'
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}