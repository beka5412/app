<?php 

namespace Backend\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Backend\Models\User;
use Backend\Models\Purchase;

class Customer extends Model
{
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function purchases() : HasMany
    {
        return $this->hasMany(Purchase::class);
    }
}