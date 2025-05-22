<?php

namespace Backend\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaylabChargebackAlert extends Model
{
    public $fillable = [
        'alert_id',
        'api_alert_id',
        'api_transaction_date',
        'api_amount',
        'api_auth_code',
        'api_card_number',
        'api_merchant',
        'api_merchant_descriptor',
        'api_received_date',
        'api_issuer',
        'api_transaction_type',
        'api_source',
        'api_status',
        'api_type',
        'paylab_result_status',
        'order_id',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
