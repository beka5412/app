<?php

namespace Backend\Entities\Abstracts;
use Backend\Services\RocketPanel\RocketPanel;
use Ezeksoft\PHPWriteLog\Log;
use Setono\MetaConversionsApi\Event\Event as FBEvent;
use Setono\MetaConversionsApi\Pixel\Pixel as FBPixel;
use Setono\MetaConversionsApi\Client\Client as FBClient;
use Backend\Enums\PagarMe\EPagarMeChargeType;
use Backend\Enums\PagarMe\EPagarMeChargeStatus;
use Backend\Enums\Order\EOrderStatus;
use Backend\Enums\Order\EOrderStatusDetail;
use Backend\Enums\Purchase\EPurchaseStatus;
use Backend\Enums\Product\EProductDelivery;
use Backend\Enums\OrderMeta\EOrderMetaPaymentMethod;
use Backend\Enums\PagarMe\EPagarMePaymentMethod;
use Backend\Enums\Subscription\ESubscriptionStatus;
use Backend\Services\OneSignal\OneSignal;
use Ezeksoft\RocketZap\SDK as RocketZap;
use Ezeksoft\RocketZap\Enum\{ProjectType, Event as RocketZapEvent, PaymentMethod as RocketZapPaymentMethod};
use Ezeksoft\RocketZap\Exception\
{
    CustomerRequiredException as RocketZapCustomerRequiredException, 
    EventRequiredException as RocketZapEventRequiredException,
    ProductsRequiredException as RocketZapProductsRequiredException,
    OrderRequiredException as RocketZapOrderRequiredException
};
use Backend\Notifiers\Email\Mailer as Email;
use Backend\Services\RocketMember\RocketMember;
use Backend\Entities\BestsellerEntity;
use Backend\Entities\Abstracts\SellerBalance;
use Backend\Entities\Abstracts\AffiliateBalance;
use Backend\Entities\Abstracts\CustomerSubscription;
use Backend\Models\User;
use Backend\Models\Product;
use Backend\Models\Order;
use Backend\Models\OrderMeta;
use Backend\Models\Purchase;
use Backend\Models\Customer;
use Backend\Models\Balance;
use Backend\Models\Checkout;
use Backend\Models\Pixel;
use Backend\Models\Subscription;
use Backend\Models\Invoice;

class PaymentWebhook
{
    public static function update($body, $options=[])
    {
        $now = date('Y-m-d H:i:s');
        extract($body);
        $response = [];
        $options = (object) $options;
        $gateway = $options->gateway ?? '';


        // encontra transacao no banco de dados (se for recorrente procurar pelo id da assinatura e caso seja unico, procura pelo id da cobranca)
        $order = Order::where('transaction_id', $is_recurring_by_id ? $subscription_id : $transaction_id)->first();
        if (empty($order)) return ["status" => "error", "message" => "[$now] O pedido não foi localizado."];
        
        // se o pedido nao estava aprovado, entao esse eh o primeiro pagamento e nao um re-pagamento via recorrencia
        $prev_o_status = $order->status;
        $prev_o_status_detail = $order->status_detail;


        // usuarios
        $customer = Customer::find($order->customer_id);
        $user = User::find($order->user_id);
        

        // busca os produtos comprados separando em duas categorias: "padrao do checkout" e "extras"
        extract(purchased_products($order));

        
        $meta_payment_method = $order->meta('info_payment_method'); // metodo de pagamento (das metas do pedido)
        $checkout = Checkout::find($order->checkout_id); // obtem checkout utilizado na transacao

        
        /** 
         * Configura envio de WhatsApp
         */

        $aux = explode(" ", $customer->name ?? '');
        $customer_first_name = $aux[0];
        $customer_last_name = substr($customer->name ?? '', strlen($customer_first_name) + 1, strlen($customer->name ?? ''));

        $rocketzap = null;
        if ($user->user_id_rocket_panel)
        {
            $rocketzap = RocketZap::SDK($user->user_id_rocket_panel);
            
            // adaptar para executar no localhost (Ezequiel)
            if (str_contains(env('URL'), env('EZEQUIEL_LOCAL_IF_URL_CONTAINS_THIS'))) $rocketzap->setEndpoint(env('EZEQUIEL_LOCAL_ROCKETZAP_ENDPOINT'));

            $r_customer = $rocketzap->customer()
                ->setId($customer->id)
                ->setFirstName($customer_first_name)
                ->setLastName($customer_last_name)
                ->setEmail($customer->email)
                ->setPhone($customer->phone);

            foreach ($products_base as $product)
            {
                $rocketzap->addProduct(
                    $rocketzap->product()
                        ->setId($product->id)
                        ->setName($product->name)
                        ->setPrice($product->price)
                );
            }

            $merchant = $rocketzap->merchant()
                ->setId($user->id)
                ->setName($user->name)
                ->setEmail($user->email); 

            // mais abaixo executa os envios...
        }




        /*
        |------------------------------------------------------
        | Checa o status do pagamento: aprovado, cancelado, ...
        |------------------------------------------------------
        */

        
        /**
         * Aprovado
         */

        if ($type == $options->status->APPROVED)
        {
            $response = ["status" => "success", "message" => "Aprovado."];

            // atualiza status do pedido
            $order->status = EOrderStatus::APPROVED;
            $order->status_details = EOrderStatusDetail::APPROVED;
            $order->save();


            SellerBalance::credit($order, $meta_payment_method);    // paga o vendedor
            AffiliateBalance::credit($order, $meta_payment_method); // paga o afiliado


            // ativa a assinatura
            $subscription = Subscription::where('order_id', $order->id)->first();
            if (!empty($subscription))
            {
                $subscription->status = ESubscriptionStatus::ACTIVE;
                $subscription->expires_at = date("Y-m-d H:i:s", strtotime(today()." + $subscription->interval_count $subscription->interval"));
                $subscription->save();

                // marca ultima fatura como paga
                $invoice = Invoice::where('order_id', $order->id)->orderBy('id', 'DESC')->first();
                if (empty($invoice))
                {
                    $invoice = new Invoice;
                    $invoice->order_id = $order->id;
                }
                
                if ($gateway == 'pagarme') $invoice->meta = json_encode(["pagarme_charge_id" => $transaction_id]);

                $invoice->paid_at = today();
                $invoice->paid = true;
                $invoice->save();
                

                // cria nova fatura
                $invoice = new Invoice;
                $invoice->order_id = $order->id;
                $invoice->due_date = date("Y-m-d H:i:s", strtotime(today()." + $subscription->interval_count $subscription->interval"));
                $invoice->paid = false;
                $invoice->save();
            }


            $purchases = [];
            foreach ($products as $product)
            {

                /**
                 * Cria produto para o cliente
                 * obs: cada purchase eh um produto disponivel na area de compras
                 */

                // caso ja exista esse produto na lista de compras do cliente, apenas reativar
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
                $purchases[] = $purchase;


                /**
                 * Envia para a RocketMember
                 */
                if ($product->delivery == EProductDelivery::ROCKET_MEMBER->value) $rocketmember = RocketMember::payload([
                    "status" => EOrderStatus::APPROVED,
                    "status_detail" => EOrderStatusDetail::APPROVED,
                    "product_id" => $product->id,
                    "product_sku" => $product->sku,
                    "customer_id" => $customer->id,
                    "user_id" => $user->id,
                    "order_id" => $order->id,
                ])
                ->send();
                

                /**
                 * Contabiliza vendas para classificacao de best seller
                 */
                
                if ($prev_o_status <> EOrderStatus::APPROVED) BestsellerEntity::increment($product?->id);


                /**
                 * Envia webhook
                 */

                // Postback::payload([
                //     "status" => EOrderStatus::APPROVED,
                //     "status_detail" => EOrderStatusDetail::APPROVED,
                //     "product_id" => $product->id,
                //     "product_sku" => $product->sku,
                //     "customer_id" => $customer->id,
                //     "user_id" => $user->id,
                //     "order_id" => $order->id,
                // ])->send();


                echo "<pre>";
                if ($order->checkout_id)
                {
                    // anual
                    if ($order->checkout_id == Checkout::where('sku', '653FC26E59B04')->first()?->id)
                    {
                        $response = RocketPanel::payload([
                            'user' => [
                                "email" => $customer->email,
                                "name" => $customer->name,
                                "password" => $customer->doc
                            ],
                            "expires_at" => date("Y-m-d H:i:s", strtotime(today()." + 1 year")),
                            'platforms' => [17, 16, 15], // rocketplanner, rocketbots, rocketlink 
                        ])->send();

                        echo "VITALICIO: \n";
                        echo "\n=====================\n";
                        print_r($response);
                    }

                    // vitalicio
                    if ($order->checkout_id == Checkout::where('sku', '6549AF5F928A5')->first()?->id)
                    {
                        $response = RocketPanel::payload([
                            'user' => [
                                "email" => $customer->email,
                                "name" => $customer->name,
                                "password" => $customer->doc
                            ],
                            "expires_at" => date("Y-m-d H:i:s", strtotime(today()." + 10 years")),
                            'platforms' => [17, 16, 15], // rocketplanner, rocketbots, rocketlink 
                        ])->send();

                        echo "ANUAL \n";
                        echo "\n=====================\n";
                        print_r($response);
                    }
                }

                // anual:
                // https://checkout.rocketpays.app/653FC6825D51D/653FC26E59B04

                // vitalicio:
                // https://checkout.rocketpays.app/6549AF5F928A5


            }


            /**
             * Contabiliza vendas do checkout
             */

            if (!empty($checkout) && $prev_o_status <> EOrderStatus::APPROVED)
            {
                $checkout->sales = intval($checkout->sales) + 1;
                $checkout->save();
            }


            /**
             * Envia e-mail
             */

            $email_vars = compact('customer', 'products', 'order', 'purchases');
            
            Email::to($customer->email)
                ->title('Compra aprovada')
                ->subject("Você comprou " . join(", ", $product_names))
                ->body(Email::view('approvedPurchaseCustomer', $email_vars))
                ->send();


            /**
             * Pixel
             */

            // se existem mais de 1 produto no checkout (excluindo orderbumps e outros adicionais)
            // utilizar o primeiro pixel encontrado
            $facebook_pixels = [];
            foreach ($products_base as $product_base)
            {
                $pixels = Pixel::where('user_id', $product_base->user_id)->where('product_id', $product_base->id)->get();
                foreach ($pixels as $pixel)
                {
                    if ($pixel->platform == "facebook" && $pixel->access_token) 
                    {
                        $facebook_pixels[] = $pixel;
                        goto end_of_pixel_fetch;
                    }
                }
            }
            
            end_of_pixel_fetch:
                
            // deleta dados sensiveis para uma variavel que sera salva em log
            $aux = json_decode(json_encode($facebook_pixels));
            $pixels_ = array_filter($aux, function($item) {
                unset($item->access_token);
                return $item;
            });
            
            (new Log)->write(base_path('logs/pixel.log'), "\n============================\n[$now]\n" . json_encode($pixels_));

            foreach ($facebook_pixels as $pixel)
            {
                // pixel do facebook
                if ($pixel->platform == "facebook" && $pixel->access_token)
                {
                    $echo_pixel = "pixel: $pixel->content\n";

                    $event = new FBEvent(FBEvent::EVENT_PURCHASE);
                    $event->pixels[] = new FBPixel($pixel->content, $pixel->access_token);

                    if (!empty($event->pixels))
                    {
                        $user_url = null;
                        if ($user_domain = $pixel->domain->full_domain) $user_url = "https://$user_domain";
                        $event->eventSourceUrl = ($user_url ?: get_subdomain_serialized('checkout'))."/".$checkout->sku;
                        $event->customData->currency = 'BRL';
                        $event->customData->value = $order->total;
                        $event->customData->orderId = $order->id;
                        $event->customData->contentName = join(", ", $product_names);
                        $event->customData->contentType = sizeof($products) > 1 ? 'product_group' : 'product';
                        $event->customData->numItems = sizeof($products);
                        $event->customData->deliveryCategory = 'home_delivery';
                                                                // in_store – a compra exige que o cliente entre na loja.
                                                                // curbside – a compra exige a retirada externa.
                                                                // home_delivery – a compra é entregue ao cliente.
                        foreach ($products as $product)
                        {
                            $event->customData->contentIds[] = $product->sku;
                            $event->customData->contents[] = ["id" => $product->sku, "quantity" => 1];
                        }

                        // informacoes do usuario
                        $event->userData->email[] = $customer->email;
                        $event->userData->phoneNumber[] = $customer->phone;
                        $aux = explode(" ", $customer->name);
                        $customer_first_name = $aux[0];
                        $customer_last_name = substr($customer->name, strlen($customer_first_name) + 1, strlen($customer->name));
                        $event->userData->firstName[] =  $customer_first_name;
                        $event->userData->lastName[] = $customer_last_name;
                        if (!empty($customer->gender)) $event->userData->gender[] = $customer->gender;
                        if (!empty($customer->birthdate)) $event->userData->dateOfBirth[] = $customer->birthdate;
                        if (!empty($customer->address_city)) $event->userData->city[] = $customer->address_city;
                        if (!empty($customer->address_state)) $event->userData->state[] = $customer->address_state;
                        if (!empty($customer->address_zipcode)) $event->userData->zipCode[] = $customer->address_zipcode;
                        if (!empty($customer->address_country)) $event->userData->country[] = $customer->address_country;
                        if (!empty($customer->ip)) $event->userData->clientIpAddress = $customer->ip;
                        if (!empty($customer->user_agent)) $event->userData->clientUserAgent = $customer->user_agent;

                        $client = new FBClient;
                        $client->sendEvent($event);

                        (new Log)->write(base_path('logs/pixel.log'), "$echo_pixel | ".json_encode([
                            "url" => $event->eventSourceUrl,
                            "custom" => $event->customData,
                            "user" => $event->userData,
                        ]));

                        // print_r($event->eventSourceUrl);
                        // print_r($event->userData);
                        // print_r($event->customData);
                    }
                }
            }

            (new Log)->write(base_path('logs/pixel.log'), "----------------------------\n\n");


            /**
             * Envia mensagem para a RocketZap
             */

            try
            {
                if (in_array($meta_payment_method, [EOrderMetaPaymentMethod::CREDIT_CARD->value, EOrderMetaPaymentMethod::BILLET->value, EOrderMetaPaymentMethod::PIX->value]) && !empty($rocketzap))
                {
                    $rocketzap
                        ->setEvent(RocketZapEvent::APPROVED)
                        ->setOrder($rocketzap->order()->setId($order->uuid)->setTotal($order->total))
                        ->setPaymentMethod(match($meta_payment_method) {
                            EOrderMetaPaymentMethod::PIX->value => RocketZapPaymentMethod::PIX, 
                            EOrderMetaPaymentMethod::BILLET->value => RocketZapPaymentMethod::BILLET, 
                            EOrderMetaPaymentMethod::CREDIT_CARD->value => RocketZapPaymentMethod::CREDIT_CARD
                        })
                        ->setCustomer($r_customer)
                        ->setMerchant($merchant)
                        ->save([ProjectType::AUTOMATION]);

                    (new Log)->write(base_path('logs/rocketzap.log'), ["now" => today(), "request" => $rocketzap->getJson()]);
                    list($automation) = $rocketzap->getResponses();
                    $automation->http->finally(fn($response) => (new Log)->write(base_path('logs/rocketzap.log'), $response->getJson()));
                }
            }
            
            catch (RocketZapCustomerRequiredException|RocketZapEventRequiredException|RocketZapProductsRequiredException|RocketZapOrderRequiredException $ex)
            {
                $status = ["status" => "success", "status_detail" => "warning", "message" => $ex->getMessage()];
            }


            /**
             * Notificação push com OneSignal
             */
            $onesignal = new OneSignal;
            $onesignal->setTitle("Venda realizada!");
            $onesignal->setDescription("Sua Comissão: R$ ".currency($order->total_seller));
            $onesignal->addExternalUserID($user->email);    
            $onesignal->pushNotification();
        }


        /**
         * Pagamento rejeitado
         */

        if ($type == $options->status->PAYMENT_FAILED)
        {
            $response = ["status" => "error", "message" => "O pagamento foi recusado."];

            $status_detail = EOrderStatusDetail::CANCELED;

            $email_data = (object) [
                'title' => 'Compra cancelada',
                'subject' => strtr("Cancelamento de :products", [":products" => join(", ", $product_names)]),
                'view' => 'canceledPurchaseCustomer'
            ];

            $rocketzap_event = RocketZapEvent::CANCELED;
        }
    
        /**
         * Reembolsado
         */

        if ($type == $options->status->REFUNDED)
        {
            $response = ["status" => "error", "message" => "O pagamento foi reembolsado."];

            $status_detail = EOrderStatusDetail::REFUNDED;

            $email_data = (object) [
                'title' => 'Compra reembolsada',
                'subject' => strtr("Reembolso de :products", [":products" => join(", ", $product_names)]),
                'view' => 'refundedPurchaseCustomer'
            ];

            $rocketzap_event = RocketZapEvent::REFUNDED;
        }


        /**
         * Estornado
         */

        if ($type == $options->status->CHARGEDBACK)
        {
            $response = ["status" => "error", "O pagamento foi estornado."];

            $status_detail = EOrderStatusDetail::CHARGEDBACK;

            $email_data = (object) [
                'title' => 'Compra contestada',
                'subject' => strtr("Chargeback de :products", [":products" => join(", ", $product_names)]),
                'view' => 'chargedbackPurchaseCustomer'
            ];

            $rocketzap_event = RocketZapEvent::CHARGEDBACK;
        }


        /**
         * Gerou pix
         */

        if ($payment_method == $options->payment_method->PIX && $status == $options->status->PENDING)
        {
            $response = ["status" => "pending", "Um pix foi gerado."];

            $status_detail = EOrderStatusDetail::PIX_GENERATED;

            $order->status = EOrderStatus::PENDING;
            $order->status_details = $status_detail;
            $order->save();

            /**
             * Envia e-mail
             */
            
            Email::to($customer->email)
                ->title('Pix gerado')
                ->subject("Compra pendente de " . join(", ", $product_names))
                ->body(Email::view('pixGeneratedPurchaseCustomer', $email_vars=compact('customer', 'products', 'order')))
                ->send();
        }


        /**
         * Gerou boleto
         */

        if ($payment_method == $options->payment_method->BILLET && $status == $options->status->PENDING)
        {
            $response = ["status" => "pending", "Um boleto foi gerado."];

            $status_detail = EOrderStatusDetail::BILLET_PRINTED;

            $order->status = EOrderStatus::PENDING;
            $order->status_details = $status_detail;
            $order->save();

            /**
             * Envia e-mail
             */
            
            Email::to($customer->email)
                ->title('Boleto gerado')
                ->subject("Compra pendente de ".join(", ", $product_names))
                ->body(Email::view('billetPrintedPurchaseCustomer', $email_vars=compact('customer', 'products', 'order')))
                ->send();
        }
        

        /**
         * Dinheiro devolvido
         */

        if (in_array($type, [$options->status->REFUNDED, $options->status->CHARGEDBACK]))
        {

            /**
             * Ajusta saldo dos usuarios que participaram da venda
             */

            SellerBalance::debit($order, $meta_payment_method); // tira ganho do vendedor
            AffiliateBalance::debit($order, $meta_payment_method); // tira ganho do afiliado
        }


        /**
         * Pedido cancelado
         */

        if (in_array($type, [$options->status->PAYMENT_FAILED, $options->status->REFUNDED, $options->status->CHARGEDBACK]))
        {
            CustomerSubscription::cancel($order, $status_detail);
            CustomerSubscription::cancelServices(compact('order', 'email_data', 'rocketzap_event', 'products', 'status_detail', 'user',
                'meta_payment_method', 'customer', 'products_base'));
        }

        return $response;
    }
}