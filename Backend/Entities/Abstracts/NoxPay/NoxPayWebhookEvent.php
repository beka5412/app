<?php

namespace Backend\Entities\Abstracts\NoxPay;
use Backend\Entities\Abstracts\NoxPay\Events\Invoice\Invoice;

class NoxPayWebhookEvent
{
    public static function invoice()
    {
        return new Invoice;
    }
}