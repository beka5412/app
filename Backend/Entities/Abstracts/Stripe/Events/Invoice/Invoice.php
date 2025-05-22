<?php

namespace Backend\Entities\Abstracts\Stripe\Events\Invoice;

use Backend\Types\Stripe\StripeWebhookType;

class Invoice
{
    public function paid(StripeWebhookType|array $webhook_queue_object)
    {
        return (new InvoicePaid($webhook_queue_object))->response();
    }

    public function payment_failed(StripeWebhookType|array $webhook_queue_object)
    {
        return (new InvoicePaymentFailed($webhook_queue_object))->response();
    }
}