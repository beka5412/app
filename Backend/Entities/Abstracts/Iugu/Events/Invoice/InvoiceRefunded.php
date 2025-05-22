<?php

namespace Backend\Entities\Abstracts\Iugu\Events\Invoice;

use Backend\Entities\Abstracts\Astronmembers\AstronmembersQueue;
use Backend\Entities\Abstracts\Memberkit\MemberkitQueue;
use Backend\Entities\Abstracts\SellerBalance;
use Backend\Entities\Abstracts\Sellflux\SellfluxQueue;
use Backend\Entities\Abstracts\Utmify\UtmifyQueue;
use Backend\Enums\Order\EOrderStatus;
use Backend\Enums\Order\EOrderStatusDetail;
use Backend\Enums\Product\EProductDelivery;
use Backend\Enums\Purchase\EPurchaseStatus;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Models\AppAstronmembersIntegration;
use Backend\Models\AppMemberkitIntegration;
use Backend\Models\AppSellfluxIntegration;
use Backend\Models\AppUtmifyIntegration;
use Backend\Models\Checkout;
use Backend\Models\Customer;
use Backend\Models\Order;
use Backend\Models\Product;
use Backend\Models\Purchase;
use Backend\Models\User;
use Backend\Types\Astronmembers\AstronmembersQueueData;
use Backend\Types\Memberkit\MemberkitQueueData;
use Backend\Types\Response\EResponseDataStatus;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;
use Backend\Types\Sellflux\SellfluxQueueData;
use Backend\Types\Utmify\EUtmifyEvent;
use Backend\Types\Utmify\EUtmifyPaymentMethod;

class InvoiceRefunded
{
    public function response(Request $request)
    {
        $data = $request->query('data');
        $id = $data['id'];

        $order = Order::where('transaction_id', $id)->where('status', 'pending')->first();
        if (!$order) return Response::json(
            new ResponseData([
                'status' => EResponseDataStatus::ERROR,
                'message' => 'Order not found or has already been paid.'
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




        /**
         * Lógica após as validações
         */

        $order->status = EOrderStatus::CANCELED;
        $order->status_details = EOrderStatusDetail::REFUNDED;
        $order->save();


        SellerBalance::debit($order);



        /**
         * Cancela compra
         */

        $purchases = Purchase::where('order_id', $order->id)->get();
        foreach ($purchases as $purchase)
        {
            $purchase->status = EPurchaseStatus::CANCELED;
            $purchase->save();
        }


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
                "status" => EUtmifyEvent::REFUSED,
                "createdAt" => date("Y-m-d H:i:s", strtotime(today() . ' + 3 hours')),
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

        if ($product->delivery === EProductDelivery::MEMBERKIT->value)
        {
            $memberkit_integration = AppMemberkitIntegration::where('user_id', $product->user_id)->where('product_id', $product->id)->first();
            if (!$memberkit_integration || !$memberkit_integration->status) goto END_MEMBERKIT;

            $memberkit_payload = [
                'full_name' => $customer->name,
                'email' => $customer->email,
                'classroom_ids' => json_decode($memberkit_integration->classroomids ?? ''),
                'status' => 'inactive',
                'blocked' => true
            ];

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
                'uri' => '/removeClubUser',
                'verb' => 'DELETE',
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode("$username:$password")
                ],
                'query_string' => [
                    'club_id' => $astronmembers_integration->clubid,
                    'user_id' => $customer->email
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
                "status" => "estornou",
                "url" => get_subdomain_serialized('checkout') . "/" . $checkout->sku,
                "payment_method" => "cartao-credito",
                "product_id" => $product->id,
                "product_name" => $product->name,
                "transaction_value" => $order->total_seller,
                "ip" => get_ordermeta($order->id, 'ip'),
                "tags" => ["reembolsou"],
                "remove_tags" => ["comprou-produto"]
            ]
        ]);

        SellfluxQueue::push($sellflux_data);

        END_SELLFLUX:


        return Response::json(
            new ResponseData([
                'status' => EResponseDataStatus::SUCCESS,
                'message' => 'Payment refunded.'
            ]),
            new ResponseStatus('200 OK')
        );
    }
}
