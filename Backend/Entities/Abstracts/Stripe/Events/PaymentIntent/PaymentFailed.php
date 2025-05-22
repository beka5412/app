<?php

namespace Backend\Entities\Abstracts\Stripe\Events\PaymentIntent;

use Backend\Entities\Abstracts\SellerBalance;
use Backend\Entities\Abstracts\Stripe\StripeWebhookQueue;
use Backend\Entities\Abstracts\Utmify\UtmifyQueue;
use Backend\Enums\EmailTemplate\EEmailTemplateType;
use Backend\Enums\Order\EOrderStatus;
use Backend\Enums\Order\EOrderStatusDetail;
use Backend\Enums\OrderMeta\EOrderMetaPaymentMethod;
use Backend\Enums\Purchase\EPurchaseStatus;
use Backend\Http\Response;
use Backend\Models\AppUtmifyIntegration;
use Backend\Models\Checkout;
use Backend\Models\Customer;
use Backend\Models\Order;
use Backend\Models\Product;
use Backend\Models\Purchase;
use Backend\Models\StripePaymentIntent;
use Backend\Models\User;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;
use Backend\Types\Stripe\Entity\EStripePaymentIntentStatus;
use Backend\Types\Stripe\Events\EStripePaymentIntent;
use Backend\Types\Stripe\StripeWebhookType;
use Backend\Types\Utmify\EUtmifyEvent;
use Backend\Types\Utmify\EUtmifyPaymentMethod;
use Exception;

class PaymentFailed
{
    public StripeWebhookType|array $webhook_queue_object;

    public function __construct(StripeWebhookType|array $webhook_queue_object)
    {
        $this->webhook_queue_object = $webhook_queue_object;
    }

    public function response()
    {
        $webhook_queue_object = $this->webhook_queue_object;

        if (gettype($webhook_queue_object) === "array") $webhook_queue_object = (object) $webhook_queue_object;

        $body = json_decode($webhook_queue_object->data);
        $entity = $webhook_queue_object->entity;
        $obj = $body->data->object;
        $payment_intent_id = $obj->id ?? null;

        if (!$payment_intent_id)
            return StripeWebhookQueue::response($entity, new ResponseData(['status' => 'error', 'message' => 'Payment intent is not present.']), new ResponseStatus('400 Bad Request'));
       
        $stripe_payment_intent = StripePaymentIntent::where('payment_intent', $payment_intent_id)->first();
        if (!$stripe_payment_intent)
            return StripeWebhookQueue::response($entity, new ResponseData(['status' => 'error', 'message' => 'Payment intent not found.']), new ResponseStatus('404 Bad Request'));

        $stripe_payment_intent->status = EStripePaymentIntentStatus::PAYMENT_FAILED;
        $stripe_payment_intent->save();

        $response_data = new ResponseData(['status' => 'success', 'message' => 'Payment failed.']);
        $response_code = new ResponseStatus('200 OK');

        return Response::json($response_data, $response_code);
    }
}
