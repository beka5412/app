<?php

namespace Backend\Entities\Abstracts\Stripe;

use Backend\Entities\Abstracts\Stripe\Events\Charge\Charge;
use Backend\Entities\Abstracts\Stripe\Events\Invoice\Invoice;
use Backend\Entities\Abstracts\Stripe\Events\Dispute\Dispute;
use Backend\Entities\Abstracts\Stripe\Events\PaymentIntent\PaymentIntent;

class StripeWebhookEvent
{
    public static function charge()
    {
        return new Charge;
    }

    public static function invoice()
    {
        return new Invoice;
    }

    public static function dispute()
    {
        return new Dispute;
    }

    public static function payment_intent()
    {
        return new PaymentIntent;
    }
}
