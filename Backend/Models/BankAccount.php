<?php 

namespace Backend\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Backend\Models\User;
use Backend\Models\Bank;

class BankAccount extends Model
{
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bank() : BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }
}