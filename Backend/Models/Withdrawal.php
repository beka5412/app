<?php 

namespace Backend\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Backend\Models\User;

class Withdrawal extends Model
{
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}