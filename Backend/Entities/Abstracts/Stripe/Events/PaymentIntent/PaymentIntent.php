<?php

namespace Backend\Entities\Abstracts\Stripe\Events\PaymentIntent;

use Backend\Types\Stripe\StripeWebhookType;

class PaymentIntent
{
    public function payment_failed(StripeWebhookType|array $webhook_queue_object)
    {
        return (new PaymentFailed($webhook_queue_object))->response();
    }
}
