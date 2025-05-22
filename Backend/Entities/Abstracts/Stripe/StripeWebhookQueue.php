<?php

namespace Backend\Entities\Abstracts\Stripe;

use Backend\Models\WebhookQueue;

use Backend\Http\Response;
use Backend\Types\Response\ResponseStatus;
use Backend\Types\Response\ResponseData;
use Backend\Types\Stripe\Events\EStripeCharge;
use Backend\Types\Stripe\Events\EStripeInvoice;
use Backend\Types\Stripe\Events\EStripePaymentIntent;
use Backend\Types\Stripe\StripeWebhookType;

class StripeWebhookQueue
{
    public static function push(?string $data, string $date = '', bool $bypass = false): void
    {
        $webhook_queue = new WebhookQueue;
        $webhook_queue->data = $data;
        $webhook_queue->status = 'waiting';
        $webhook_queue->bypass = $bypass ? 1 : 0;
        $webhook_queue->scheduled_at = $date ? $date : today();
        $webhook_queue->save();
    }

    public static function run(WebhookQueue $webhook_queue)
    {
        self::receive([
            "data" => $webhook_queue->data,
            "bypass" => $webhook_queue->bypass,
            "entity" => $webhook_queue
        ]);
    }

    public static function receive(StripeWebhookType|array $webhook_queue_object)
    {
        $body = json_decode($webhook_queue_object instanceof StripeWebhookType ? $webhook_queue_object->data : $webhook_queue_object['data']);
        $type = $body->type ?? '';

        if ($type === EStripeCharge::FAILED->value) return StripeWebhookEvent::charge()->failed($webhook_queue_object);
        else if ($type === EStripeCharge::SUCCEEDED->value) return StripeWebhookEvent::charge()->succeeded($webhook_queue_object);
        else if ($type === EStripeCharge::DISPUTE_CLOSE->value) return StripeWebhookEvent::dispute()->dispute_closed($webhook_queue_object);
        else if ($type === EStripeCharge::REFUNDED->value) return StripeWebhookEvent::charge()->refunded($webhook_queue_object);
        else if ($type === EStripeInvoice::PAID->value) return StripeWebhookEvent::invoice()->paid($webhook_queue_object);
        else if ($type === EStripeInvoice::PAYMENT_FAILED->value) return StripeWebhookEvent::invoice()->payment_failed($webhook_queue_object);
        else if ($type === EStripePaymentIntent::PAYMENT_FAILED->value) return StripeWebhookEvent::payment_intent()->payment_failed($webhook_queue_object);
    }

    public static function response(?WebhookQueue $webhook_queue, ResponseData $response_data, ResponseStatus $response_code): Response
    {
        if (($webhook_queue ?? null) instanceof WebhookQueue)
        {
            $webhook_queue->response = json_encode([
                "status" => $response_code->status,
                "data" => $response_data->message ?? '' // $response_data
            ]);

            $webhook_queue->save();
        }

        return Response::json($response_data, $response_code);
    }
}
