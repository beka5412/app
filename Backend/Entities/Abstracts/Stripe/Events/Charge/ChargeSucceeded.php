<?php

namespace Backend\Entities\Abstracts\Stripe\Events\Charge;

use Backend\Entities\Abstracts\Astronmembers\AstronmembersQueue;
use Backend\Entities\Abstracts\Memberkit\MemberkitQueue;
use Backend\Entities\Abstracts\SellerBalance;
use Backend\Entities\Abstracts\Sellflux\SellfluxQueue;
use Backend\Entities\Abstracts\Stripe\StripeWebhookQueue;
use Backend\Entities\Abstracts\Utmify\UtmifyQueue;
use Backend\Enums\EmailTemplate\EEmailTemplatePath;
use Backend\Enums\EmailTemplate\EEmailTemplateType;
use Backend\Enums\Order\EOrderStatus;
use Backend\Enums\Order\EOrderStatusDetail;
use Backend\Enums\OrderMeta\EOrderMetaPaymentMethod;
use Backend\Enums\Product\EProductDelivery;
use Backend\Enums\Purchase\EPurchaseStatus;
use Backend\Enums\Subscription\ESubscriptionStatus;
use Backend\Models\AppAstronmembersIntegration;
use Backend\Models\AppMemberkitIntegration;
use Backend\Models\AppSellfluxIntegration;
use Backend\Models\AppUtmifyIntegration;
use Backend\Models\Checkout;
use Backend\Models\Customer;
use Backend\Models\Order;
use Backend\Models\Product;
use Backend\Models\Purchase;
use Backend\Models\StripePaymentIntent;
use Backend\Models\User;
use Backend\Types\Astronmembers\AstronmembersQueueData;
use Backend\Types\Memberkit\MemberkitQueueData;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;
use Backend\Types\Stripe\Entity\EStripePaymentIntentStatus;
use Backend\Types\Stripe\StripeWebhookType;
use Backend\Types\Utmify\EUtmifyEvent;
use Backend\Types\Utmify\EUtmifyPaymentMethod;
use Backend\Services\OneSignal\OneSignal;
use Backend\Models\Invoice as InvoiceDB;
use Backend\Models\Subscription;
use Backend\Services\RocketMember\RocketMember;
use Backend\Types\Sellflux\SellfluxQueueData;
use Exception;

class ChargeSucceeded
{
    public StripeWebhookType|array $webhook_queue_object;

    public function __construct(StripeWebhookType|array $webhook_queue_object)
    {
        $this->webhook_queue_object = $webhook_queue_object;
    }


    /**
     * NOTA:
     * O pagamento de recorrência também gera um charge.succeeded só que ele não é executado aqui
     * pois não vem com um metadata.order_id, já que esse parâmetro não é definido quando é criado
     * a assinatura.
     * 
     * O primeiro pagamento da recorrência é executado aqui, não através de assinatura, mas de um
     * payment_intent. A assinatura é criada como "Incompleta" e só é cobrada no próximo ciclo.
     * Ou seja, esse payment_intent serviu como a primeira cobrança.
     */
    public function response()
    {
        $webhook_queue_object = $this->webhook_queue_object;

        if (gettype($webhook_queue_object) === "array") $webhook_queue_object = (object) $webhook_queue_object;

        $body = json_decode($webhook_queue_object->data);
        $entity = $webhook_queue_object->entity;
        $obj = $body->data->object;
        $meta = $obj->metadata ?? null;
        $meta_order_id = $meta->order_id ?? null;
        $charge_id = $obj->id ?? '';
        $payment_intent_id = $obj->payment_intent ?? '';
        $testmode_key = $body->testmode_key ?? '';

        $stripe_conf = [
            'api_key' => stripe_secret($testmode_key),
            'stripe_version' => '2023-10-16',
        ];
        
        if (env('STRIPE_CONNECT') == 'true' && env('STRIPE_CONNECT_ACCOUNT')) $stripe_conf['stripe_account'] = env('STRIPE_CONNECT_ACCOUNT');

        $stripe = new \Stripe\StripeClient($stripe_conf);

        $stripe_payment_intent = StripePaymentIntent::where('payment_intent', $payment_intent_id)->first();
        if ($stripe_payment_intent)
        {
            $stripe_payment_intent->status = EStripePaymentIntentStatus::SUCCEEDED;
            $stripe_payment_intent->save();
        }

        if (!$meta_order_id)
            return StripeWebhookQueue::response($entity, new ResponseData(['status' => 'error', 'message' => 'Metadata is not present.']), new ResponseStatus('400 Bad Request'));

        $order = Order::where('uuid', $meta_order_id)->first();
        if (empty($order))
            return StripeWebhookQueue::response($entity, new ResponseData(['status' => 'error', 'message' => 'Order not found.']), new ResponseStatus('400 Bad Request'));

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

        if ($order->status === EOrderStatus::APPROVED->value)
            return StripeWebhookQueue::response($entity, new ResponseData(['status' => 'error', 'message' => 'This order has already been paid.']), new ResponseStatus('200 OK'));



        /**
         * Lógica após as validações
         */

        $total_int = intval(doubleval($order->total) * 100);
        $no_password = !$customer->password;

        $customer_password = '';
        if ($no_password)
        {
            $customer_password = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
            $customer->password = hash_make($customer_password);
            $customer->save();
        }
        $order->status = EOrderStatus::APPROVED;
        $order->status_details = EOrderStatusDetail::APPROVED;
        $order->reason = 'Aprovado.';
        $order->save();

        
        /**
         * Ativar assinatura
         */

        $subscription = null;
        if ($order->gateway_subscription_id)
        {
            $subscription = Subscription::where('order_id', $order->id)->first();
            if ($subscription)
            {
                $invoice = InvoiceDB::where('order_id', $order->id)->orderBy('id', 'DESC')->first();
                if (!empty($invoice))
                {
                    $invoice->meta = json_encode(["stripe_charge_id" => $obj->id]);
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
            }
        }


        SellerBalance::credit($order);


        /**
         * Splita
         */

        // if (env('STRIPE_CONNECT') == 'true')
        // {
        //     try
        //     {
        //         $stripe->transfers->create([
        //             'amount' => $total_int,
        //             'currency' => 'brl',
        //             'destination' => env('STRIPE_CONNECT_ACCOUNT'),
        //             'source_transaction' => $charge_id,
        //         ]);
        //     }
        //     catch (Exception $ex)
        //     {
        //         // TODO: registrar como um aviso na resposta de que o split não foi realizado
        //     }
        // }


        /**
         * Envia e-mail
         */

        $email_data = [
            "site_url" => site_url(),
            "platform" => site_name(),
            "username" => $customer->name,
            "image" => site_url() . $product->image,
            "product_name" => $product->name,
            "total" => number_to_currency_by_symbol($order->currency_total, $order->currency_symbol),
            "symbol" => currency_code_to_symbol($order->currency_symbol),
            "email" => $customer->email,
            "password" => $customer_password,
            "login_url" => get_subdomain_serialized('purchase') . "/login/token/$customer->one_time_access_token",
            "product_author" => $product->author,
            "product_support_email" => $product->support_email,
            "product_warranty" => $product->warranty_time,
            "transaction_id" => $order->uuid
        ];

        send_email($customer->email, $email_data, $no_password ? EEmailTemplatePath::PURCHASE_APPROVED_WITH_PASSWORD : EEmailTemplatePath::PURCHASE_APPROVED, $order->lang);


        /**
         * Libera compra
         */

        $purchase = Purchase::where('customer_id', $order->customer_id)->where('product_id', $product->id)->first();
        if (empty($purchase))
        {
            $purchase = new Purchase;
            $purchase->customer_id = $order->customer_id;
            $purchase->product_id = $product->id;
        }
        $purchase->order_id = $order->id;
        $purchase->status = EPurchaseStatus::ACTIVE;
        $purchase->save();


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
                "createdAt" => date("Y-m-d H:i:s", strtotime(today() . ' + 3 hours')),
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
         * Memberkit 
         */

        if ($product->delivery === EProductDelivery::MEMBERKIT->value)
        {
            $memberkit_integration = AppMemberkitIntegration::where('user_id', $product->user_id)->where('product_id', $product->id)->first();
            if (!$memberkit_integration || !$memberkit_integration->status) goto END_MEMBERKIT;

            $memberkit_payload = [
                'full_name' => $customer->name,
                'email' => $customer->email,
                'status' => 'active',
                'classroom_ids' => json_decode($memberkit_integration->classroomids ?? ''),
                'blocked' => false
            ];

            // if ($subscription->expires_at ?? false) 
            //     $memberkit_payload['expires_at'] = $subscription->expires_at;
            // else 
                $memberkit_payload['unlimited'] = true;

            $memberkit_data = new MemberkitQueueData([
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'query_string' => [
                    "api_key" => aes_decode_db($memberkit_integration->apikey)
                ],
                'payload' => $memberkit_payload
            ]);

            MemberkitQueue::push($memberkit_data);
        }

        END_MEMBERKIT:


        /**
         * Astronmembers
         */

        if ($product->delivery === EProductDelivery::ASTRONMEMBERS->value)
        {
            $astronmembers_integration = AppAstronmembersIntegration::where('user_id', $product->user_id)->where('product_id', $product->id)->first();
            if (!$astronmembers_integration || !$astronmembers_integration->status) goto END_ASTRONMEMBERS;

            $username = aes_decode_db($astronmembers_integration->username);
            $password = aes_decode_db($astronmembers_integration->password);

            $astronmembers_data = new AstronmembersQueueData([
                'uri' => '/createClubUser',
                'verb' => 'POST',
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Basic ' . base64_encode("$username:$password")
                ],
                'payload' => [
                    'club_id' => $astronmembers_integration->clubid,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'password' => rand(1000000000, 9999999999),
                    'send_welcome' => 1,
                ]
            ]);

            AstronmembersQueue::push($astronmembers_data);
        }

        END_ASTRONMEMBERS:


        /**
         * Sellflux
         */
     
        $sellflux_integration = AppSellfluxIntegration::where('user_id', $product->user_id)->where('product_id', $product->id)->first();
        if (!$sellflux_integration || !$sellflux_integration->status) goto END_SELLFLUX;

        $sellflux_link = $sellflux_integration->link ? aes_decode_db($sellflux_integration->link) : '';
        if (!$sellflux_link) goto END_SELLFLUX;
        
        $sellflux_expiration = strtotime(($subscription->expires_at ?? false) ? $subscription->expires_at : today()." + 3 days");

        $sellflux_data = new SellfluxQueueData([
            'uri' => $sellflux_link,
            'verb' => 'POST',
            'payload' => [
                "name" => $customer->name,
                "email" => $customer->email,
                "phone" => $customer->phone ?: '',
                "gateway" => env('APP_NAME'),
                "transaction_id" => $order->id,
                "offer_id" => $order->uuid,
                "status" => "compra-realizada",
                "payment_date" => date("Y-m-d\TH:i:s.".(explode(" ", microtime())[1])."-03"),
                "url" => get_subdomain_serialized('checkout')."/".$checkout->sku,
                "payment_method" => "cartao-credito",
                "expiration_date" => date("Y-m-d\TH:i:s.".(explode(" ", microtime())[1])."-03", $sellflux_expiration),
                "product_id" => $product->id,
                "product_name" => $product->name,
                "transaction_value" => $order->total_seller,
                "ip" => get_ordermeta($order->id, 'ip'),
                "tags" => ["comprou-produto"],
                "remove_tags" => ["falha", "abandonou-carrinho"]
            ]
        ]);

        SellfluxQueue::push($sellflux_data);

        END_SELLFLUX:

         
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


        /**
         * RocketMember
         */

        if ($product->delivery == EProductDelivery::ROCKETMEMBER->value) $rocketmember = RocketMember::payload([
            "status" => EOrderStatus::APPROVED,
            "status_detail" => EOrderStatusDetail::APPROVED,
            "product_id" => $product->id,
            "product_sku" => $product->sku,
            "customer_id" => $customer->id,
            "user_id" => $user->id,
            "order_id" => $order->id,
        ])
        ->send();


        $response_data = new ResponseData(['status' => 'success', 'message' => 'Order paid.']);
        $response_code = new ResponseStatus('200 OK');

        return StripeWebhookQueue::response($entity, $response_data, $response_code);
    }
}
