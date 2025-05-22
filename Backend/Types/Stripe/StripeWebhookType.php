<?php

namespace Backend\Types\Stripe;

use Backend\Models\WebhookQueue;

class StripeWebhookType
{
    public ?string $data;

    public bool $bypass = false;
    public ?WebhookQueue $entity;

    public function __construct(WebhookQueue|array $object)
    {
        if ($object instanceof WebhookQueue)
        {
            $this->data = $object->data;
            $this->bypass = $object->bypass;
            $this->entity = $object;
        }
        else
        {
            $this->data = $object["data"];
            $this->bypass = $object["bypass"];
            $this->entity = $object["entity"];
        }
    }
}
