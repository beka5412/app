<?php

declare(strict_types=1);

namespace Backend\Entities\Abstracts\Iugu;

use Backend\Entities\Abstracts\SellerBalance;
use Backend\Entities\Abstracts\Sellflux\SellfluxQueue;
use Backend\Entities\Abstracts\Utmify\UtmifyQueue;
use Backend\Enums\Purchase\EPurchaseStatus;
use Backend\Enums\Subscription\ESubscriptionStatus;
use Backend\Exceptions\Iugu\IuguRequestErrorException;
use Backend\Http\Response;
use Backend\Models\AppSellfluxIntegration;
use Backend\Models\AppUtmifyIntegration;
use Backend\Models\Checkout;
use Backend\Models\Customer;
use Backend\Models\IuguChargeQueue as ModelsIuguChargeQueue;
use Backend\Services\Iugu\IuguRest;
use Backend\Types\Iugu\EIuguChargeQueueStatus;
use Backend\Types\Iugu\IuguChargeQueueDataList;
use Backend\Types\Response\EResponseDataStatus;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;
use Backend\Types\Iugu\IuguChargeType;
use Backend\Models\Invoice as ModelsInvoice;
use Backend\Models\Order;
use Backend\Models\Product;
use Backend\Models\Purchase;
use Backend\Models\Subscription;
use Backend\Models\User;
use Backend\Services\OneSignal\OneSignal;
use Backend\Types\Sellflux\SellfluxQueueData;
use Backend\Types\Utmify\EUtmifyEvent;
use Backend\Types\Utmify\EUtmifyPaymentMethod;
use Exception;

class IuguChargeQueue
{
    public static function push(IuguChargeQueueDataList $data, string $date = ''): ModelsIuguChargeQueue
    {
        $queue = new ModelsIuguChargeQueue;
        $queue->data = json_encode(["hash" => aes_encode_db(json_encode($data))]);
        $queue->status = EIuguChargeQueueStatus::WAITING;
        $queue->scheduled_at = $date ?: today();
        $queue->order_id = $data->order_id;
        $queue->save();
        return $queue;
    }

    public static function run(ModelsIuguChargeQueue $queue)
    {
        self::send([
            "data" => $queue->data,
            "entity" => $queue
        ]);
    }

    public static function send(IuguChargeType|array $queue_object): Response
    {
        $data = json_decode($queue_object instanceof IuguChargeType ? $queue_object->data : $queue_object['data']);
        $hash = $data->hash ?? '';
        $hash_object = json_decode(aes_decode_db($hash));
        $data = $hash_object->data ?? null;
        $entity = $queue_object instanceof IuguChargeType ? $queue_object->entity : $queue_object['entity'];

        if (!$data)
        {
            $response_data = new ResponseData(['status' => 'error', 'message' => 'Error decoding data.']);
            $response_code = new ResponseStatus('400 Bad Request');

            return self::response($entity, $response_data, $response_code);
        }

        try
        {
            $body_token = $data->token;
            $headers_token = (array) $body_token->headers ?? [];
            $payload_token = $body_token->payload ?? null;
            $query_string_token = !empty($body_token->query_string ?? null) ? "?" . http_build_query($body_token->query_string ?? []) : '';
            $verb_token = $body_token->verb ?? null;
            $uri_token = $body_token->uri ?? null;

            $response_token = IuguRest::request(
                verb: $verb_token,
                url: "$uri_token$query_string_token",
                headers: $headers_token,
                body: json_encode($payload_token),
                timeout: 10
            );

            $token = $response_token->json->id ?? '';


            $body_charge = $data->charge;
            $headers_charge = (array) $body_charge->headers ?? [];
            $payload_charge = $body_charge->payload ?? null;
            $query_string_charge = !empty($body_charge->query_string ?? null) ? "?" . http_build_query($body_charge->query_string ?? []) : '';
            $verb_charge = $body_charge->verb ?? null;
            $uri_charge = $body_charge->uri ?? null;
            $payload_charge->token = $token;

            $response_charge = IuguRest::request(
                verb: $verb_charge,
                url: "$uri_charge$query_string_charge",
                headers: $headers_charge,
                body: json_encode($payload_charge),
                timeout: 30
            );

            $transaction_id = $response_charge->json->invoice_id ?? '';
            $transaction_status = $response_charge->json->status ?? '';

            $entity->response = $response_charge->body;

            if (!$transaction_id)
            {
                $entity->status = EIuguChargeQueueStatus::ERROR;
                $entity->save();
                $response_data = new ResponseData(['status' => EResponseDataStatus::ERROR, 'message' => 'Iugu request error.']);
                $response_code = new ResponseStatus('400 Bad Request');
                throw new IuguRequestErrorException;
            }

            if ($transaction_status <> 'captured')
            {
                $entity->status = EIuguChargeQueueStatus::ERROR;
                $entity->save();
                $response_data = new ResponseData(['status' => EResponseDataStatus::ERROR, 'message' => 'Transaction failed.']);
                $response_code = new ResponseStatus('400 Bad Request');
                throw new IuguRequestErrorException;
            }


            $order = Order::find($data->meta->order_id);
            if (!$order) return Response::json(
                new ResponseData([
                    'status' => EResponseDataStatus::ERROR,
                    'message' => 'Order not found.'
                ]),
                new ResponseStatus('400 Bad Request')
            );

            $subscription = Subscription::where('order_id', $order->id)->first();
            if (!$subscription) return Response::json(
                new ResponseData([
                    'status' => EResponseDataStatus::ERROR,
                    'message' => 'Subscription not found.'
                ]),
                new ResponseStatus('400 Bad Request')
            );

            $invoice = ModelsInvoice::where('order_id', $order->id)->orderBy('id', 'DESC')->first();
            if (!$invoice) return Response::json(
                new ResponseData([
                    'status' => EResponseDataStatus::ERROR,
                    'message' => 'Invoice not found.'
                ]),
                new ResponseStatus('400 Bad Request')
            );

            $customer = Customer::find($order->customer_id);
            if (!$customer) return Response::json(
                new ResponseData([
                    'status' => EResponseDataStatus::ERROR,
                    'message' => 'Customer not found.'
                ]),
                new ResponseStatus('400 Bad Request')
            );

            $user = User::find($order->user_id);
            if (!$user) return Response::json(
                new ResponseData([
                    'status' => EResponseDataStatus::ERROR,
                    'message' => 'Seller not found.'
                ]),
                new ResponseStatus('400 Bad Request')
            );

            $checkout = Checkout::where('id', $order->checkout_id)->first();
            if (!$checkout) return Response::json(
                new ResponseData([
                    'status' => EResponseDataStatus::ERROR,
                    'message' => 'Checkout not found.'
                ]),
                new ResponseStatus('400 Bad Request')
            );

            $product = Product::where('id', $checkout->product_id)
                ->with([
                    'orderbumps' => function ($query)
                    {
                        $query->with('product', 'product_as_checkout');
                    }
                ])
                ->first();
            if (!$product) return Response::json(
                new ResponseData([
                    'status' => EResponseDataStatus::ERROR,
                    'message' => 'Product not found.'
                ]),
                new ResponseStatus('400 Bad Request')
            );

            $entity->status = EIuguChargeQueueStatus::SENT;
            $entity->save();

            
            
            $response_verb = $response_charge->verb;
            $response_url = $response_charge->url;
            $response_errno = $response_charge->errno;
            $response_error = $response_charge->error;
            $response_status_code = $response_charge->status_code;
            $response_time = $response_charge->time;
            $response_body = $response_charge->body;
            $now = today();

            $order->log = json_encode($response_charge);
            $order->save();




            /**
             * Ativa assinatura
             */

            $invoice->paid_at = today();
            $invoice->paid = true;
            $invoice->save();

            // $date_base = $subscription->expires_at ?: today();
            // $expires_at = date('Y-m-d H:i:s', strtotime("$date_base + $subscription->interval_count $subscription->interval"));
            $expires_at = date('Y-m-d H:i:s', strtotime(today() . " + $subscription->interval_count $subscription->interval"));

            $subscription->status = ESubscriptionStatus::ACTIVE;
            $subscription->expires_at = $expires_at;
            $subscription->save();

            $invoice = new ModelsInvoice;
            $invoice->order_id = $order->id;
            $invoice->due_date = $expires_at;
            $invoice->paid = false;
            $invoice->save();




            /**
             * Agenda a cobrança para o próximo ciclo
             */

            IuguChargeQueue::push(
                new IuguChargeQueueDataList([
                    'token' => [
                        'verb' => 'POST',
                        'uri' => '/payment_token',
                        'headers' => $headers_token,
                        'query_string' => null,
                        'payload' => $payload_token
                    ],
                    'charge' => [
                        'verb' => 'POST',
                        'uri' => '/charge?api_token=' . env('IUGU_API_TOKEN'),
                        'headers' => $headers_charge,
                        'query_string' => null,
                        'payload' => $payload_charge
                    ],
                    'meta' => [
                        'order_id' => $order->id,
                        'subscription_id' => $subscription->id,
                        'invoice_id' => $invoice->id
                    ]
                ]), 
                $expires_at
            );




            /**
             * Credita saldo para o vendedor
             */
            
            SellerBalance::credit($order);




            /**
             * Reativa compra
             */

            $purchase = Purchase::where('customer_id', $order->customer_id)->where('product_id', $product->id)->first();
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
             * Sellflux
             */

            $sellflux_integration = AppSellfluxIntegration::where('user_id', $product->user_id)->where('product_id', $product->id)->first();
            if (!$sellflux_integration || !$sellflux_integration->status) goto END_SELLFLUX;

            $sellflux_link = $sellflux_integration->link ? aes_decode_db($sellflux_integration->link) : '';
            if (!$sellflux_link) goto END_SELLFLUX;

            $sellflux_expiration = strtotime(($subscription->expires_at ?? false) ? $subscription->expires_at : today() . " + 3 days");

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
                    "payment_date" => date("Y-m-d\TH:i:s." . (explode(" ", microtime())[1]) . "-03"),
                    "url" => get_subdomain_serialized('checkout') . "/" . $checkout->sku,
                    "payment_method" => "cartao-credito",
                    "expiration_date" => date("Y-m-d\TH:i:s." . (explode(" ", microtime())[1]) . "-03", $sellflux_expiration),
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

            try
            {
                $os_symbol = currency_code_to_symbol($order->currency_symbol)->value;
                $os_amount = number_to_currency_by_symbol($order->total_seller / (get_setting($order->currency_symbol . '_brl') ?: 1), $order->currency_symbol);

                $onesignal = new OneSignal;
                $onesignal->setTitle(__('Sale made!'));
                $onesignal->setDescription(__('Your Commission:') . " $os_symbol $os_amount");
                $onesignal->addExternalUserID($user->email);
                $onesignal->pushNotification();
            }

            catch (Exception)
            {
                // TODO: adicionar erro a lista de erros
            }

            $response_data = new ResponseData([
                'status' => EResponseDataStatus::SUCCESS,
                'message' => 'Iugu request sent.',
                'data' => compact('response_token', 'response_charge')
            ]);
            $response_code = new ResponseStatus('200 OK');
        }

        catch (Exception | IuguRequestErrorException)
        {
            $response_data = new ResponseData(['status' => EResponseDataStatus::ERROR, 'message' => 'Iugu request error.']);
            $response_code = new ResponseStatus('400 Bad Request');
        }

        return self::response($entity, $response_data, $response_code);
    }

    public static function response(?ModelsIuguChargeQueue $queue, ResponseData $response_data, ResponseStatus $response_code): Response
    {
        if (($queue ?? null) instanceof ModelsIuguChargeQueue)
        {
            $queue->response = json_encode([
                "status" => $response_code->status,
                "message" => $response_data->message ?? '',
                "data" => $response_data->data ?? ''
            ]);
            if ($response_data->status === EResponseDataStatus::ERROR) $queue->status = EIuguChargeQueueStatus::ERROR;
            $queue->save();
        }

        return Response::json($response_data, $response_code);
    }
    public static function cancel(string|int $order_id): void
    {
        ModelsIuguChargeQueue::where('order_id', $order_id)->where('status', EIuguChargeQueueStatus::WAITING->value)
            ->orderBy('created_at', 'DESC')->delete();
    }
}
