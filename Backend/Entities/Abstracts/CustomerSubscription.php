<?php

namespace Backend\Entities\Abstracts;

use Backend\Models\Balance;
use Backend\Enums\OrderMeta\EOrderMetaPaymentMethod;
use Backend\Enums\Order\EOrderStatus;
use Backend\Enums\Order\EOrderStatusDetail;
use Backend\Enums\Purchase\EPurchaseStatus;
use Backend\Enums\Product\EProductDelivery;
use Backend\Notifiers\Email\Mailer as Email;
use Backend\Enums\Subscription\ESubscriptionStatus;
use Backend\Models\Invoice;
use Ezeksoft\PHPWriteLog\Log;
use Ezeksoft\RocketZap\SDK as RocketZap;
use Ezeksoft\RocketZap\Enum\{ProjectType, Event as RocketZapEvent, PaymentMethod as RocketZapPaymentMethod};
use Ezeksoft\RocketZap\Exception\
{
    CustomerRequiredException as RocketZapCustomerRequiredException, 
    EventRequiredException as RocketZapEventRequiredException,
    ProductsRequiredException as RocketZapProductsRequiredException,
    OrderRequiredException as RocketZapOrderRequiredException
};
use Backend\Models\Order;
use Backend\Models\Subscription;
use Backend\Models\Purchase;
use Backend\Services\RocketMember\RocketMember;

class CustomerSubscription
{
    public static function cancel($order, $status_detail)
    {

        /**
         * Cancela o pedido
         */

        $order->status = EOrderStatus::CANCELED;
        $order->status_details = $status_detail;
        $order->save();


        /**
         * Cancela assinatura
         */

        $subscription = Subscription::where('order_id', $order->id)->first();
        if (!empty($subscription))
        {
            $subscription->status = ESubscriptionStatus::CANCELED;
            $subscription->save();
        }


        /**
         * Cancela produtos
         */

        Purchase::where('order_id', $order->id)->update(['status' => EPurchaseStatus::CANCELED->value]);
        

        /**
         * Cancela faturas
         */

        Invoice::where('order_id', $order->id)->where('paid', '<>', 1)->orderBy('created_at', 'DESC')->delete();
    }

    public static function cancelServices($data)
    {
        extract($data);
        

        /**
         * Envia para a RocketMember
         */

        foreach ($products as $product)
        {
            if ($product->delivery == EProductDelivery::ROCKETMEMBER->value) RocketMember::payload([
                "status" => EOrderStatus::CANCELED,
                "status_detail" => $status_detail,
                "product_id" => $product->id,
                "product_sku" => $product->sku,
                "customer_id" => $customer->id,
                "user_id" => $user->id,
                "order_id" => $order->id,
            ])
            ->send();
        }


        /**
         * Envia e-mail
         */

        $purchase = Purchase::where('order_id', $order->id)->first();
        Email::to($customer->email)
            ->title($email_data->title)
            ->subject($email_data->subject)
            ->body(Email::view($email_data->view, $email_vars=compact('customer', 'products', 'order', 'purchase')))
            ->send();
        

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


        /**
         * Envia mensagem para a RocketZap
         */

        try
        {
            if (in_array($meta_payment_method, [EOrderMetaPaymentMethod::CREDIT_CARD->value, EOrderMetaPaymentMethod::BILLET->value, EOrderMetaPaymentMethod::PIX->value]) && !empty($rocketzap))
            {
                $rocketzap
                    ->setEvent($rocketzap_event)
                    ->setOrder($rocketzap->order()->setId($order->uuid)->setTotal($order->total))
                    ->setPaymentMethod(match($meta_payment_method) {
                        EOrderMetaPaymentMethod::PIX->value => RocketZapPaymentMethod::PIX, 
                        EOrderMetaPaymentMethod::BILLET->value => RocketZapPaymentMethod::BILLET, 
                        EOrderMetaPaymentMethod::CREDIT_CARD->value => RocketZapPaymentMethod::CREDIT_CARD, 
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
            echo $ex->getMessage();
        }
    }
}