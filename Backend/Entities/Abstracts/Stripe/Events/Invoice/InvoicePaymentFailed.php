<?php

namespace Backend\Entities\Abstracts\Stripe\Events\Invoice;

use Backend\Entities\Abstracts\SellerBalance;
use Backend\Entities\Abstracts\Stripe\StripeWebhookQueue;
use Backend\Entities\Abstracts\Utmify\UtmifyQueue;
use Backend\Enums\EmailTemplate\EEmailTemplateType;
use Backend\Enums\Order\EOrderStatus;
use Backend\Enums\Order\EOrderStatusDetail;
use Backend\Enums\OrderMeta\EOrderMetaPaymentMethod;
use Backend\Enums\Purchase\EPurchaseStatus;
use Backend\Enums\Subscription\ESubscriptionStatus;
use Backend\Models\AppUtmifyIntegration;
use Backend\Models\Checkout;
use Backend\Models\Customer;
use Backend\Models\Order;
use Backend\Models\Product;
use Backend\Models\Purchase;
use Backend\Models\Invoice as InvoiceDB;
use Backend\Models\Subscription;
use Backend\Models\User;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;
use Backend\Types\Stripe\StripeWebhookType;
use Backend\Types\Utmify\EUtmifyEvent;
use Backend\Types\Utmify\EUtmifyPaymentMethod;
use Exception;

class InvoicePaymentFailed
{
    public StripeWebhookType|array $webhook_queue_object;

    public function __construct(StripeWebhookType|array $webhook_queue_object)
    {
        $this->webhook_queue_object = $webhook_queue_object;
    }

    /**
     * Pagamento de uma recorrência de assinatura
     */
    public function response()
    {
        $webhook_queue_object = $this->webhook_queue_object;

        if (gettype($webhook_queue_object) === "array") $webhook_queue_object = (object) $webhook_queue_object;

        $body = json_decode($webhook_queue_object->data);
        $entity = $webhook_queue_object->entity;

        $obj = $body->data->object;
        $subscription_id = $obj->subscription ?? '';

        if (!$subscription_id)
            return StripeWebhookQueue::response($entity, new ResponseData(['status' => 'error', 'message' => 'Subscription not found.']), new ResponseStatus('400 Bad Request'));

        $order = Order::where('gateway_subscription_id', $subscription_id)->first();

        if (empty($order))
            return StripeWebhookQueue::response($entity, new ResponseData(['status' => 'error', 'message' => 'Order not found.']), new ResponseStatus('400 Bad Request'));
        
        $customer = Customer::find($order->customer_id);
        if (empty($customer))
            return StripeWebhookQueue::response($entity, new ResponseData(['status' => 'error', 'message' => 'Customer not found.']), new ResponseStatus('400 Bad Request'));

        $checkout = Checkout::where('id', $order->checkout_id)->first();
        if (empty($checkout))
            return StripeWebhookQueue::response($entity, new ResponseData(['status' => 'error', 'message' => 'Checkout not found.']), new ResponseStatus('400 Bad Request'));

        $product = Product::where('id', $checkout->product_id)
            ->with([
                'orderbumps' => function ($query)
                {
                    $query->with('product', 'product_as_checkout');
                }
            ])
            ->first();
        if (empty($product))
            return StripeWebhookQueue::response($entity, new ResponseData(['status' => 'error', 'message' => 'Product not found.']), new ResponseStatus('400 Bad Request'));


        /**
         * Cancela assinatura
         */

        $subscription = Subscription::where('order_id', $order->id)->first();

        if (empty($subscription))
            return StripeWebhookQueue::response($entity, new ResponseData(['status' => 'error', 'message' => 'Subscription not found.']), new ResponseStatus('400 Bad Request'));

        if ($subscription->status === ESubscriptionStatus::CANCELED->value)
            return StripeWebhookQueue::response($entity, new ResponseData(['status' => 'error', 'message' => 'This order has already been canceled.']), new ResponseStatus('200 OK'));

        $subscription->status = ESubscriptionStatus::CANCELED;
        $subscription->save();

        $response_data = new ResponseData(['status' => 'success', 'message' => 'Invoice payment failed.']);
        $response_code = new ResponseStatus('200 OK');

        return StripeWebhookQueue::response($entity, $response_data, $response_code);
    }
}
