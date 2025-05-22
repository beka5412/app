<?php

namespace Backend\Entities\Abstracts\Stripe\Events\Dispute;

use Backend\Types\Stripe\StripeWebhookType;

class Dispute
{
    public function dispute_closed(StripeWebhookType|array $webhook_queue_object)
    {
        return (new DisputeClosed($webhook_queue_object))->response();
    }
}
