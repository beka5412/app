<?php

namespace Backend\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AwardRequest extends Model
{
    public $fillable = [
        'user_id',
        'award_id',
        'status',
        'user_address_id'
    ];

    public function award(): BelongsTo
    {
        return $this->belongsTo(Award::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
