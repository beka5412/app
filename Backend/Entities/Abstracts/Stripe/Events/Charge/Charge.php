<?php

namespace Backend\Entities\Abstracts\Stripe\Events\Charge;

use Backend\Types\Stripe\StripeWebhookType;

class Charge
{
    public function succeeded(StripeWebhookType|array $webhook_queue_object)
    {
        return (new ChargeSucceeded($webhook_queue_object))->response();
    }

    public function failed(StripeWebhookType|array $webhook_queue_object)
    {
        return (new ChargeFailed($webhook_queue_object))->response();
    }

    public function refunded(StripeWebhookType|array $webhook_queue_object)
    {
        return (new ChargeRefunded($webhook_queue_object))->response();
    }

    public function dispute_closed(StripeWebhookType|array $webhook_queue_object)
    {
        return (new ChargeDisputeClosed($webhook_queue_object))->response();
    }
}
