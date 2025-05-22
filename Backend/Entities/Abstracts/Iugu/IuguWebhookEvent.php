<?php

namespace Backend\Entities\Abstracts\Iugu;

use Backend\Entities\Abstracts\Iugu\Events\Invoice\Invoice;
use Backend\Entities\Abstracts\Iugu\Events\Subscription\Subscription;

class IuguWebhookEvent
{
    public static function invoice()
    {
        return new Invoice;
    }

    public static function subscription()
    {
        return new Subscription;
    }
}
