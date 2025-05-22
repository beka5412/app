<?php

namespace Backend\Entities\Abstracts\Stripe\Events\Invoice;

use Backend\Entities\Abstracts\SellerBalance;
use Backend\Entities\Abstracts\Stripe\StripeWebhookQueue;
use Backend\Entities\Abstracts\Utmify\UtmifyQueue;
use Backend\Enums\Order\EOrderStatus;
use Backend\Enums\OrderMeta\EOrderMetaPaymentMethod;
use Backend\Enums\Subscription\ESubscriptionStatus;
use Backend\Models\AppUtmifyIntegration;
use Backend\Models\Checkout;
use Backend\Models\Customer;
use Backend\Models\Order;
use Backend\Models\Product;
use Backend\Models\Invoice as InvoiceDB;
use Backend\Models\StripePaymentIntent;
use Backend\Models\Subscription;
use Backend\Models\User;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;
use Backend\Types\Stripe\Entity\EStripePaymentIntentStatus;
use Backend\Types\Stripe\StripeWebhookType;
use Backend\Types\Utmify\EUtmifyEvent;
use Backend\Types\Utmify\EUtmifyPaymentMethod;
use Backend\Services\OneSignal\OneSignal;
use Exception;

class InvoicePaid
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
        $charge_id = $obj->charge ?? null;
        $amount_paid = $obj->amount_paid ?? null;
        $payment_intent_id = $obj->payment_intent ?? null;
        $testmode_key = $body->testmode_key ?? '';
        
        $stripe_conf = [
            'api_key' => stripe_secret($testmode_key),
            'stripe_version' => '2023-10-16',
        ];
        
        if (env('STRIPE_CONNECT') == 'true' && env('STRIPE_CONNECT_ACCOUNT')) $stripe_conf['stripe_account'] = env('STRIPE_CONNECT_ACCOUNT');

        $stripe = new \Stripe\StripeClient($stripe_conf);

        // uma compra que é feita no upsell, já vai cair no charge.succeeded
        // precisa de um identificador para dar um breakpoint nesse metodo
        // charge_event_was_executed

        if (!$subscription_id)
            return StripeWebhookQueue::response($entity, new ResponseData(['status' => 'error', 'message' => 'Subscription is not present.']), new ResponseStatus('400 Bad Request'));

        $order = Order::where('gateway_subscription_id', $subscription_id)->first();
        if (empty($order))
            return StripeWebhookQueue::response($entity, new ResponseData(['status' => 'error', 'message' => 'Order not found.']), new ResponseStatus('400 Bad Request'));

        // if ($order->skip_invoice_paid)
        // {
        //     $order->skip_invoice_paid = 0;
        //     $order->save();
        //     return StripeWebhookQueue::response($entity, new ResponseData(['status' => 'error', 'message' => 'First charge event has already been executed.']), new ResponseStatus('400 Bad Request'));
        // }

        $user = User::find($order->user_id);
        
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

        $subscription = Subscription::where('order_id', $order->id)->first();
        if (empty($subscription))
            return StripeWebhookQueue::response($entity, new ResponseData(['status' => 'error', 'message' => 'Subscription not found.']), new ResponseStatus('400 Bad Request'));

        if (!$payment_intent_id)
            return StripeWebhookQueue::response($entity, new ResponseData(['status' => 'error', 'message' => 'Payment intent is not present.']), new ResponseStatus('200 OK'));

        $invoice_exists = false;
        $invoice_paid = InvoiceDB::where('order_id', $order->id)->where('gateway_invoice_id', $obj->id)->orderBy('id', 'DESC')->first();
        $invoice_exists = !empty($invoice_paid);
        $total_int = intval(doubleval($order->total) * 100);

        if ($invoice_exists)
            return StripeWebhookQueue::response($entity, new ResponseData(['status' => 'error', 'message' => 'Invoice already been paid.']), new ResponseStatus('200 OK'));

        $stripe_payment_intent = new StripePaymentIntent;
        $stripe_payment_intent->payment_intent = $payment_intent_id;
        $stripe_payment_intent->order_id = $order->id;
        $stripe_payment_intent->status = EStripePaymentIntentStatus::SUCCEEDED;
        $stripe_payment_intent->save();

        /**
         * Ativar assinatura
         */

        $invoice = InvoiceDB::where('order_id', $order->id)->orderBy('id', 'DESC')->first();
        if (!empty($invoice))
        {
            $invoice->meta = json_encode(["stripe_invoice_id" => $obj->id]);
            $invoice->paid_at = today();
            $invoice->paid = true;
            $invoice->gateway_invoice_id = $obj->id;
            $invoice->stripe_payment_intent_id = $stripe_payment_intent->id;
            $invoice->save();
        }

        $subscription->status = ESubscriptionStatus::ACTIVE;
        $date_base = $subscription->expires_at ? $subscription->expires_at : today();
        $subscription->expires_at = date("Y-m-d H:i:s", strtotime($date_base . " + $subscription->interval_count $subscription->interval"));
        $subscription->save();

        $invoice = new InvoiceDB;
        $invoice->order_id = $order->id;
        $invoice->due_date = date("Y-m-d H:i:s", strtotime(today() . " + $subscription->interval_count $subscription->interval"));
        $invoice->paid = false;
        $invoice->save();


        SellerBalance::credit($order);

        
        /**
         * Splita
         */

        //  try
        //  {
        //      $stripe->transfers->create([
        //          'amount' => $total_int,
        //          'currency' => 'brl',
        //          'destination' => env('STRIPE_SPLIT_3'),
        //          'source_transaction' => $charge_id,
        //      ]);
        //  }
        //  catch (Exception $ex)
        //  {
        //      // TODO: registrar como um aviso na resposta de que o split não foi realizado
        //  }


        /**
         * Utmify 
         */

        $total_int = intval($order->total * 100);
        $platform_fee_int = intval($order->total_vendor * 100);
        $total_seller_int = intval($order->total_seller * 100);

        $utmify = AppUtmifyIntegration::where('user_id', $user->id)->first();
        if ($utmify && $utmify->apikey) UtmifyQueue::push(json_encode([
            "headers" => [
                "x-api-token" => $utmify->apikey
            ],
            "payload" => [
                "orderId" => "or_$order->uuid",
                "platform" => site_name(),
                "paymentMethod" => EUtmifyPaymentMethod::CREDIT_CARD,
                "status" => EUtmifyEvent::PAID,
                "createdAt" => date("Y-m-d H:i:s", strtotime(today().' + 3 hours')),
                "approvedDate" => date("Y-m-d H:i:s", strtotime(today() . ' + 3 hours')),
                "customer" => [
                    "name" => $customer->name,
                    "email" => $customer->email,
                    "phone" => $customer->phone ?? null,
                    "document" => $customer->doc ?? null,
                    "ip" => get_ordermeta($order->id, "ip")
                ],
                "product" => [
                    "id" => "prod_$product->id",
                    "name" => $product->name,
                    "planId" => null,
                    "planName" => null,
                    "quantity" => 1,
                    "priceInCents" => intval($order->total * 100)
                ],
                "trackingParameters" => [
                    "sck" => get_ordermeta($order->id, "tracking_sck"),
                    "src" => get_ordermeta($order->id, "tracking_src"),
                    "utm_campaign" => get_ordermeta($order->id, "tracking_utm_campaign"),
                    "utm_content" => get_ordermeta($order->id, "tracking_utm_content"),
                    "utm_medium" => get_ordermeta($order->id, "tracking_utm_medium"),
                    "utm_source" => get_ordermeta($order->id, "tracking_utm_source"),
                    "utm_term" => get_ordermeta($order->id, "tracking_utm_term")
                ],
                "commission" => [
                    "totalPriceInCents" => $total_int,
                    "gatewayFeeInCents" => $platform_fee_int,
                    "userCommissionInCents" => $total_seller_int,
                    "currency" => "BRL"
                ]
            ]
        ]));

                 
        /**
         * Notificação push com OneSignal
         */

        // TODO: onesignal deve estar em uma fila
        try
        {
            $os_symbol = currency_code_to_symbol($order->currency_symbol)->value;
            $os_amount = number_to_currency_by_symbol($order->total_seller / (get_setting($order->currency_symbol.'_brl') ?: 1), $order->currency_symbol);

            $onesignal = new OneSignal;
            $onesignal->setTitle(__('Sale made!'));
            $onesignal->setDescription(__('Your Commission:')." $os_symbol $os_amount");
            $onesignal->addExternalUserID($user->email);    
            $onesignal->pushNotification();
        }

        catch (Exception)
        {
            // TODO: adicionar erro a lista de erros
        }


        $response_data = new ResponseData(['status' => 'success', 'message' => 'Invoice paid.']);
        $response_code = new ResponseStatus('200 OK');

        return StripeWebhookQueue::response($entity, $response_data, $response_code);
    }
}
