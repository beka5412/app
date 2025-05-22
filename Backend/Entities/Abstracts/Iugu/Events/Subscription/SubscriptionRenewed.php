<?php

namespace Backend\Entities\Abstracts\Iugu\Events\Subscription;

use Backend\Entities\Abstracts\SellerBalance;
use Backend\Entities\Abstracts\Utmify\UtmifyQueue;
use Backend\Enums\Order\EOrderStatus;
use Backend\Enums\Order\EOrderStatusDetail;
use Backend\Enums\Subscription\ESubscriptionStatus;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Models\AppUtmifyIntegration;
use Backend\Models\Checkout;
use Backend\Models\Customer;
use Backend\Models\Order;
use Backend\Models\Product;
use Backend\Models\User;
use Backend\Services\OneSignal\OneSignal;
use Backend\Types\Response\EResponseDataStatus;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;
use Backend\Types\Utmify\EUtmifyEvent;
use Backend\Types\Utmify\EUtmifyPaymentMethod;
use Backend\Models\Invoice as InvoiceDB;
use Backend\Models\Subscription;
use Exception;

class SubscriptionRenewed
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

        $subscription = Subscription::where('order_id', $order->id)->first();
        if (!$subscription) return Response::json(
            new ResponseData([
                'status' => EResponseDataStatus::ERROR,
                'message' => 'Subscription not found.'
            ]),
            new ResponseStatus('400 Bad Request')
        );




        /**
         * Ativar assinatura
         */

        $invoice = InvoiceDB::where('order_id', $order->id)->orderBy('id', 'DESC')->first();
        if (!empty($invoice))
        {
            $invoice->meta = json_encode(["stripe_invoice_id" => $id]);
            $invoice->paid_at = today();
            $invoice->paid = true;
            $invoice->gateway_invoice_id = $id;
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
 
        return Response::json(
            new ResponseData([
                'status' => EResponseDataStatus::SUCCESS,
                'message' => 'Subscription paid.'
            ]),
            new ResponseStatus('200 OK')
        ); 
    }
}