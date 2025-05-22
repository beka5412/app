<?php
// arquivos .trash.php podem ser comentados do começo ao fim para não atrapalhar a execucao principal
/*
namespace Backend\Controllers\Queue;

use Backend\App;
use Backend\Entities\Abstracts\SellerBalance;
use Backend\Enums\EmailTemplate\EEmailTemplateType;
use Backend\Enums\Order\EOrderStatus;
use Backend\Enums\Order\EOrderStatusDetail;
use Backend\Enums\OrderMeta\EOrderMetaPaymentMethod;
use Backend\Enums\Purchase\EPurchaseStatus;
use Backend\Enums\Subscription\ESubscriptionStatus;
use Backend\Http\Response;
use Backend\Models\Checkout;
use Backend\Models\Customer;
use Backend\Models\Invoice;
use Backend\Models\Order;
use Backend\Models\Product;
use Backend\Models\Purchase;
use Backend\Models\Subscription;
use Backend\Models\WebhookQueue;
use Backend\Types\Response\ResponseStatus;
use Backend\Types\Response\ResponseData;
use Exception;

class StripeWebhookController_trash
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
    }

    public function handle()
    {
        $webhook_queue = WebhookQueue::where('status', 'waiting')->where('scheduled_at', '<=', today())->first();
        // if (empty($webhook_queue)) return Response::json(['status' => 'error', 'message' => 'Empty queue.']);

        // $webhook_queue->status = 'executed';
        // $webhook_queue->save();

        $body = json_decode($webhook_queue->data);
        $payload = $webhook_queue->data;
        $bypass = $webhook_queue->bypass; // parametro para se conseguir pular verificacoes em execucoes de teste
        $type = $body->type ?? '';

        $gateway_fee_percent = doubleval(get_setting('gateway_fee_percent')) / 100;
        $gateway_fee_price = doubleval(get_setting('gateway_fee_price'));

        $stripe = new \Stripe\StripeClient([
            'api_key' => env('STRIPE_SECRET'),
            'stripe_version' => '2023-10-16',
        ]);

        $obj = $body->data->object;
        $payment_intent_id = $obj->payment_intent ?? '';
        $subscription_id = $obj->subscription ?? '';
        $meta = $obj->metadata ?? null;
        $meta_order_id = $meta->order_id ?? null;

        $payment_intent = null;
        try
        {
            $payment_intent = $payment_intent_id ? $stripe->paymentIntents->retrieve($payment_intent_id) : null;
        }
        catch (Exception $ex)
        {
        }

        if ($type == 'payment_intent.created')
        {
            $response_data = new ResponseData(['status' => 'success', 'message' => 'Order created.']);
            $response_code = new ResponseStatus('200 OK');
            goto AFTER_EVENTS;
        }

        if ($type == 'transfer.created')
        {
            $response_data = new ResponseData(['status' => 'success', 'message' => 'Transfer created.']);
            $response_code = new ResponseStatus('200 OK');
            goto AFTER_EVENTS;
        }

        if ($type == 'invoice.created')
        {
            $response_data = new ResponseData(['status' => 'success', 'message' => 'Invoice created.']);
            $response_code = new ResponseStatus('200 OK');
            goto AFTER_EVENTS;
        }

        if ($type == 'invoice.finalized')
        {
            $response_data = new ResponseData(['status' => 'success', 'message' => 'Invoice finalized.']);
            $response_code = new ResponseStatus('200 OK');
            goto AFTER_EVENTS;
        }

        if ($type == 'customer.created')
        {
            $response_data = new ResponseData(['status' => 'success', 'message' => 'Customer created.']);
            $response_code = new ResponseStatus('200 OK');
            goto AFTER_EVENTS;
        }

        if ($type == 'customer.updated')
        {
            $response_data = new ResponseData(['status' => 'success', 'message' => 'Customer updated.']);
            $response_code = new ResponseStatus('200 OK');
            goto AFTER_EVENTS;
        }

        if ($type == 'price.created')
        {
            $response_data = new ResponseData(['status' => 'success', 'message' => 'Price created.']);
            $response_code = new ResponseStatus('200 OK');
            goto AFTER_EVENTS;
        }

        if ($type == 'product.updated')
        {
            $response_data = new ResponseData(['status' => 'success', 'message' => 'Product updated.']);
            $response_code = new ResponseStatus('200 OK');
            goto AFTER_EVENTS;
        }

        if ($type == 'balance.available')
        {
            $response_data = new ResponseData(['status' => 'success', 'message' => 'Balance available.']);
            $response_code = new ResponseStatus('200 OK');
            goto AFTER_EVENTS;
        }

        if ($type == 'payment_method.attached')
        {
            $response_data = new ResponseData(['status' => 'success', 'message' => 'Payment method attached.']);
            $response_code = new ResponseStatus('200 OK');
            goto AFTER_EVENTS;
        }

        if ($type == 'plan.created')
        {
            $response_data = new ResponseData(['status' => 'success', 'message' => 'Plan created.']);
            $response_code = new ResponseStatus('200 OK');
            goto AFTER_EVENTS;
        }

        if ($type == 'invoice.payment_succeeded')
        {
            $response_data = new ResponseData(['status' => 'success', 'message' => 'Plan created.']);
            $response_code = new ResponseStatus('200 OK');
            goto AFTER_EVENTS;
        }

        if ($type == 'invoiceitem.created')
        {
            $response_data = new ResponseData(['status' => 'success', 'message' => 'Invoice item created.']);
            $response_code = new ResponseStatus('200 OK');
            goto AFTER_EVENTS;
        }

        if ($type == 'payout.reconciliation_completed')
        {
            $response_data = new ResponseData(['status' => 'success', 'message' => 'Payout reconciliation completed.']);
            $response_code = new ResponseStatus('200 OK');
            goto AFTER_EVENTS;
        }

        if ($type == 'invoice.upcoming')
        {
            $response_data = new ResponseData(['status' => 'success', 'message' => 'Invoice upcoming.']);
            $response_code = new ResponseStatus('200 OK');
            goto AFTER_EVENTS;
        }



        $order = null;

        // pagamento de uma recorrencia
        if ($type === 'invoice.paid')
        {
            if ($subscription_id)
                $order = Order::where('gateway_subscription_id', $subscription_id)->first();

            // (new Log)->write(
            //     base_path("logs/stripe--invoice.paid.txt"), 
            //     "\n\n\n[".today()."] $subscription_id ".json_encode($payload)
            // );
        }

        //  TODO: verifica se quando uma recorrencia eh paga, se tbm vem o evento charge.succeeded

        // primeiro pagamento
        if ($type === 'charge.succeeded')
        {
            if ($meta_order_id)
                $order = Order::where('uuid', $meta_order_id)->first(); // pedidos de upsell
            // if (empty($order) && $payment_intent_id) $order = Order::where('transaction_id', $payment_intent_id)->first();

            // (new Log)->write(
            //     base_path("logs/stripe--charge.succeeded.txt"), 
            //     "\n\n\n[".today()."] $subscription_id ".json_encode($payload)
            // );
        }


        if (empty($order))
            return Response::json(new ResponseData(['status' => 'error', 'message' => 'Order not found.']), new ResponseStatus('400 Bad Request'));

        $customer = Customer::find($order->customer_id);
        if (empty($customer))
            return Response::json(new ResponseData(['status' => 'error', 'message' => 'Customer not found.']), new ResponseStatus('400 Bad Request'));
        $no_password = !$customer->password;

        $checkout = Checkout::where('id', $order->checkout_id)->first();
        if (empty($checkout))
            return Response::json(new ResponseData(['status' => 'error', 'message' => 'Checkout not found.']), new ResponseStatus('400 Bad Request'));

        $product = Product::where('id', $checkout->product_id)
            ->with([
                'orderbumps' => function ($query)
                {
                    $query->with('product', 'product_as_checkout');
                }
            ])
            ->first();
        if (empty($product))
            return Response::json(new ResponseData(['status' => 'error', 'message' => 'Product not found.']), new ResponseStatus('400 Bad Request'));

        $total_int = intval(doubleval($order->total) * 100);
        $total_vendor_int = intval(doubleval($order->total_vendor) * 100);
        $total_seller_int = intval(doubleval($order->total_seller) * 100);
        $gateway_fee = intval(doubleval($order->total_gateway) * 100);

        $amount_1 = $total_int * 0.0075;
        $amount_2 = $total_int * 0.0075;
        $amount_3 = $total_vendor_int - $gateway_fee - $amount_1 - $amount_2;

        $total_split_1 = intval($amount_1);
        $total_split_2 = intval($amount_2);
        $total_split_3 = intval($amount_3) + $total_seller_int;

        // echo "STRIPE: $gateway_fee\n";
        // echo "TOTAL_INT: $total_int\n";
        // echo "TOTAL_VENDOR_INT: $total_vendor_int\n";
        // echo "TOTAL_SELLER_INT: $total_seller_int\n";
        // echo "SPLIT_1: $total_split_1\n";
        // echo "SPLIT_2: $total_split_2\n";
        // echo "SPLIT_3: $total_split_3\n";
        // return;

        // TODO: tratar os eventos

        $response_data = new ResponseData(['status' => 'error', 'message' => '']);
        $response_code = new ResponseStatus('400 Bad Request');

        if ($type == 'charge.failed')
        {
            $order->status = EOrderStatus::CANCELED;
            $order->status_details = EOrderStatusDetail::REJECTED;
            $order->save();

            // TODO: desativar produtos
            $purchases = Purchase::where('order_id', $order->id)->get();
            foreach ($purchases as $purchase)
            {
                $purchase->status = EPurchaseStatus::CANCELED;
                $purchase->save();
            }

            $response_data = new ResponseData(['status' => 'error', 'message' => 'Payment failed.']);
            $response_code = new ResponseStatus('200 OK');
        }

        if ($type == 'charge.succeeded')
        {
            $charge_id = $obj->id ?? '';

            // if (1)
            if ($order->status <> EOrderStatus::APPROVED->value)
            {
                $order->status = EOrderStatus::APPROVED;
                $order->status_details = EOrderStatusDetail::APPROVED;
                $order->save();

                // $pixel = Pixel::where('product_id', $product->id)->where('platform', EPixelPlatform::FACEBOOK->value)->orderBy('id', 'DESC')->first();
                // $facebook_pixel = $pixel->content ?? '';

                // if ($facebook_pixel) file_get_contents("https://www.facebook.com/tr?id=$facebook_pixel&ev=Purchase&cd[currency]="
                // 	.(strtoupper($order->currency_symbol))."&cd[value]=$order->currency_total");

                SellerBalance::credit($order, EOrderMetaPaymentMethod::CREDIT_CARD->value);

                $response_data = new ResponseData(['status' => 'success', 'message' => 'Subscription activated.']);
                $response_code = new ResponseStatus('200 OK');

                try
                {
                    // 	$split_1 = $stripe->transfers->create([
                    // 		'amount' => $total_split_1,
                    // 		'currency' => 'brl',
                    // 		'destination' => env('STRIPE_SPLIT_1'),
                    // 		'source_transaction' => $charge_id,
                    // 	]);

                    // 	$split_2 = $stripe->transfers->create([
                    // 		'amount' => $total_split_2,
                    // 		'currency' => 'brl',
                    // 		'destination' => env('STRIPE_SPLIT_2'),
                    // 		'source_transaction' => $charge_id,
                    // 	]);

                    $split_3 = $stripe->transfers->create([
                        // 'amount' => $total_split_3,
                        'amount' => $total_int,
                        'currency' => 'brl',
                        'destination' => env('STRIPE_SPLIT_3'),
                        'source_transaction' => $charge_id,
                    ]);

                    $response_data = new ResponseData(['status' => 'success', 'message' => 'Splitted.']);
                    $response_code = new ResponseStatus('200 OK');
                }
                catch (Exception $ex)
                {
                    if ($response_data->status <> 'error')
                    {
                        $response_data = new ResponseData(['status' => 'error', 'message' => 'Split error.']);
                        $response_code = new ResponseStatus('400 Bad Request');
                    }
                }

                if ($response_data->status <> 'error')
                {
                    $response_data = new ResponseData(['status' => 'success', 'message' => 'Order paid.']);
                    $response_code = new ResponseStatus('200 OK');

                    $customer_password = '';
                    if ($no_password)
                    {
                        $customer_password = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
                        $customer->password = hash_make($customer_password);
                        $customer->save();
                    }

                    $email_data = [
                        "site_url" => site_url(),
                        "platform" => site_name(),
                        "username" => $customer->name,
                        "image" => site_url() . $product->image,
                        "product_name" => $product->name,
                        "total" => $order->total,
                        "email" => $customer->email,
                        "password" => $customer_password,
                        "login_url" => env("PROTOCOL") . "://purchase." . env("HOST") . "/login/token/$customer->one_time_access_token",
                        "product_author" => $product->author,
                        "product_support_email" => $product->support_email,
                        "product_warranty" => $product->warranty_time,
                        "transaction_id" => $order->uuid
                    ];

                    send_email($customer->email, $email_data, $no_password ? EEmailTemplateType::PURCHASE_APPROVED_WITH_PASSWORD : EEmailTemplateType::PURCHASE_APPROVED);

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
                }
            }
            else
            {
                $response_data = new ResponseData(['status' => 'error', 'message' => 'This order has already been paid.']);
                $response_code = new ResponseStatus('200 OK');
            }
        }

        if ($type == 'invoice.paid')
        {
            $charge_id = $obj->charge ?? '';

            $invoice_exists = false;

            // ativa a assinatura
            $subscription = Subscription::where('order_id', $order->id)->first();
            if (!empty($subscription))
            {
                $invoice_paid = Invoice::where('order_id', $order->id)->where('gateway_invoice_id', $obj->id)->orderBy('id', 'DESC')->first();
                $invoice_exists = !empty($invoice_paid);

                if (!$invoice_exists)
                {
                    // marca ultima fatura como paga
                    $invoice = Invoice::where('order_id', $order->id)->orderBy('id', 'DESC')->first();
                    if (!empty($invoice))
                    {
                        $invoice->meta = json_encode(["stripe_invoice_id" => $obj->id]);
                        $invoice->paid_at = today();
                        $invoice->paid = true;
                        $invoice->gateway_invoice_id = $obj->id;
                        $invoice->save();
                    }

                    $subscription->status = ESubscriptionStatus::ACTIVE;
                    $date_base = $subscription->expires_at ? $subscription->expires_at : today();
                    $subscription->expires_at = date("Y-m-d H:i:s", strtotime($date_base . " + $subscription->interval_count $subscription->interval"));
                    $subscription->save();

                    // cria nova fatura
                    $invoice = new Invoice;
                    $invoice->order_id = $order->id;
                    $invoice->due_date = date("Y-m-d H:i:s", strtotime(today() . " + $subscription->interval_count $subscription->interval"));
                    $invoice->paid = false;
                    $invoice->save();
                }
            }

            if ($invoice_exists)
            {
                $response_data = new ResponseData(['status' => 'success', 'message' => 'Invoice already been paid.']);
                $response_code = new ResponseStatus('200 OK');
                goto AFTER_EVENTS;
            }

            $splitted = false;

            SellerBalance::credit($order, EOrderMetaPaymentMethod::CREDIT_CARD->value);

            // CODIGO DE SPLIT QUE PODE SER USADO FUTURAMENTE
            // try {
            //     $split_1 = $stripe->transfers->create([
            //         'amount' => $total_split_1,
            //         'currency' => 'brl',
            //         'destination' => env('STRIPE_SPLIT_1'),
            //         'source_transaction' => $charge_id,
            //     ]);

            //     $split_2 = $stripe->transfers->create([
            //         'amount' => $total_split_2,
            //         'currency' => 'brl',
            //         'destination' => env('STRIPE_SPLIT_2'),
            //         'source_transaction' => $charge_id,
            //     ]);

            //     $split_3 = $stripe->transfers->create([
            //         'amount' => $total_split_3,
            //         'currency' => 'brl',
            //         'destination' => env('STRIPE_SPLIT_3'),
            //         'source_transaction' => $charge_id,
            //     ]);

            //     $splitted = true;

            //     (new Log)->write(
            //         base_path("logs/stripe.txt"), 
            //         "[".today()."] ".
            //         json_encode(compact('split_1', 'split_2', 'split_3'))."\n\n"
            //     );
            // } 
            // catch (Exception $ex)
            // {
            //     $splitted = false;

            //     (new Log)->write(
            //         base_path("logs/stripe.txt"), 
            //         "[".today()."] ". $ex->getMessage()
            //     );

            //     // $response_data = ['status' => 'error', 'message' => 'Split error.'];
            //     // $response_code = '400 Bad Request';
            // }

            $response_data = new ResponseData(['status' => 'success', 'message' => 'Invoice paid.', 'splitted' => $splitted]);
            $response_code = new ResponseStatus('200 OK');
        }

        AFTER_EVENTS:

        return $this->response($webhook_queue, $response_data, $response_code);
    }

    function response(WebhookQueue $webhook_queue, ResponseData $response_data, ResponseStatus $response_code)
    {
        $webhook_queue->response = json_encode([
            "status" => $response_code->status,
            "data" => $response_data->message ?? ''
        ]);

        print_r($webhook_queue->response);

        // $webhook_queue->save();

        // return Response::json($response_data, $response_code);
    }
}
*/