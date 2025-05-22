<?php

namespace Backend\Entities\Abstracts\Iugu\Events\Invoice;

use Backend\Entities\Abstracts\Astronmembers\AstronmembersQueue;
use Backend\Entities\Abstracts\Memberkit\MemberkitQueue;
use Backend\Entities\Abstracts\SellerBalance;
use Backend\Entities\Abstracts\Sellflux\SellfluxQueue;
use Backend\Entities\Abstracts\Utmify\UtmifyQueue;
use Backend\Enums\EmailTemplate\EEmailTemplatePath;
use Backend\Enums\Order\EOrderStatus;
use Backend\Enums\Order\EOrderStatusDetail;
use Backend\Enums\Product\EProductDelivery;
use Backend\Enums\Purchase\EPurchaseStatus;
use Backend\Enums\Subscription\ESubscriptionStatus;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Models\AppAstronmembersIntegration;
use Backend\Models\AppCademiIntegration;
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
use Backend\Models\Invoice as InvoiceDB;
use Backend\Models\Subscription;
use Backend\Services\Cademi\CademiRest;
use Backend\Services\OneSignal\OneSignal;
use Backend\Services\RocketMember\RocketMember;
use Exception;

class InvoicePaid
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

        $order->status = EOrderStatus::APPROVED;
        $order->status_details = EOrderStatusDetail::APPROVED;
        $order->save();

        $total_int = intval(doubleval($order->total) * 100);
        $no_password = !$customer->password;

        $customer_password = '';
        if ($no_password)
        {
            $customer_password = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
            $customer->password = hash_make($customer_password);
            $customer->save();
        }

        SellerBalance::credit($order);




        /**
         * Ativar assinatura (primeiro pagamento da assinatura)
         */

        $subscription = null;
        $subscription = Subscription::where('order_id', $order->id)->first();
        if ($subscription)
        {
            $invoice = InvoiceDB::where('order_id', $order->id)->orderBy('id', 'DESC')->first();
            if (!empty($invoice))
            {
                $invoice->paid_at = today();
                $invoice->paid = true;
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

        // TODO: onesignal deve estar em uma fila
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




        /**
         * RocketMember
         */

        if ($product->delivery == EProductDelivery::ROCKETMEMBER->value) RocketMember::payload([
            "status" => EOrderStatus::APPROVED,
            "status_detail" => EOrderStatusDetail::APPROVED,
            "product_id" => $product->id,
            "product_sku" => $product->sku,
            "customer_id" => $customer->id,
            "user_id" => $user->id,
            "order_id" => $order->id,
        ])->send();




        /**
         * Cademi
         */

        if ($product->delivery == EProductDelivery::CADEMI->value)
        {
            $cademi_integration = AppCademiIntegration::where('user_id', $product->user_id)->where('product_id', $product->id)->first();
            if (!$cademi_integration || !$cademi_integration->status) goto END_CADEMI;
            
            $cademi_subdomain = $cademi_integration->subdomain;
            $cademi_token = aes_decode_db($cademi_integration->token);
            $cademi_product_id = $cademi_integration->product_id;

            $cademi_response = CademiRest::request(
                verb: 'POST',
                url: "https://$cademi_subdomain.cademi.com.br/api/postback/custom",
                headers: ['Content-Type' => 'application/json'],
                body: json_encode([
                    "token" => $cademi_token,
                    "codigo" => $order->id,
                    "status" => "aprovado",
                    "produto_id" => $cademi_product_id,
                    "produto_nome" => $product->name,
                    "valor" => $order->total,
                    "cliente_email" => $customer->email,
                    "cliente_nome" => $customer->name,
                    "cliente_doc" => $customer->doc,
                    "cliente_celular" => $customer->phone,
                    "cliente_endereco" => $customer->address_street,
                    "cliente_endereco_n" => $customer->address_number,
                    "cliente_endereco_comp" => $customer->address_complement,
                    "cliente_endereco_bairro" => $customer->address_district,
                    "cliente_endereco_cidade" => $customer->address_city,
                    "cliente_endereco_estado" => $customer->address_state,
                    "cliente_endereco_cep" => $customer->address_zipcode,
                    "tags" => ""
                ]),
                timeout: 10
            );
            
            // if ($cademi_response->code === 200 && $cademi_response->json->data->carga->processado <> -1) { ... }
        }

        END_CADEMI:
        



        return Response::json(
            new ResponseData([
                'status' => EResponseDataStatus::SUCCESS,
                'message' => 'Pedido aprovado com sucesso.'
            ]),
            new ResponseStatus('200 OK')
        );
    }
}
