<?php 

namespace Backend\Models;

use Backend\Enums\Order\EOrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Backend\Models\BankAccount;
use Backend\Models\Balance;
use Backend\Models\Kyc;

class User extends Model
{
    public $fillable = [
        'email',
        'name',
        'password',
        'access_token',
        'rocketpanel_access_token',
        'created_by_rocket_panel'
    ];

    public function bank_account() : HasOne
    {
        return $this->hasOne(BankAccount::class);
    }

    public function balance() : HasOne
    {
        return $this->hasOne(Balance::class);
    }

    public function withLastDrawals() : HasOne
    {
        return $this->hasOne(Withdrawal::class)->limit(5);
    }

    public function awardRequests() : HasMany
    {
        return $this->hasMany(AwardRequest::class)->limit(5);
    }

    public function kyc() : HasOne
    {
        return $this->hasOne(Kyc::class);
    }

    public function lastApprovedOrders(): HasMany
    {
        return $this->hasMany(Order::class)
            ->where('status', EOrderStatus::APPROVED->value)
            ->orderBy('created_at', 'desc');
    }
}