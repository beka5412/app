<?php

namespace Backend\Controllers\Snippets;

use Backend\App;
use Backend\Entities\Abstracts\Iugu\IuguChargeQueue;
use Backend\Enums\Order\EOrderStatus;
use Backend\Enums\Order\EOrderStatusDetail;
use Backend\Enums\Pixel\EPixelPlatform;
use Backend\Enums\Product\EProductPaymentType;
use Backend\Enums\Subscription\ESubscriptionStatus;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Models\Checkout;
use Backend\Models\Customer;
use Backend\Models\Invoice;
use Backend\Models\Order;
use Backend\Models\Pixel;
use Backend\Models\StripePaymentIntent;
use Backend\Models\Subscription;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Product;
use Backend\Models\AbandonedCart;
use Backend\Exceptions\Product\ProductNotFoundException;
use Backend\Models\IuguSeller;
use Backend\Services\Iugu\IuguRest;
use Backend\Types\Iugu\IuguChargeQueueDataList;
use Backend\Types\Response\EResponseDataStatus;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;
use Backend\Types\Stripe\Entity\EStripePaymentIntentStatus;
use Exception;
use Ezeksoft\PHPWriteLog\Log;

class UpsellController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Upsell';
        $this->context = 'snippet';
        $this->indexFile = 'frontend/view/snippets/upsell/indexView.php';
        $this->user = user();
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;


        $product_id = $request->query('id');
        $url = get_current_route();

        $product = Product::find($product_id);

        View::basic($this->indexFile, compact('title', 'context', 'user', 'product', 'url'));
    }

    public function get_intent_id(Request $request)
    {
        $product_id = $request->query('id');
        $data = base64_decode($request->query('data'));
        $data = cryptoJsAesDecrypt(env('AES_HTTP_SECRET'), $data);
        $obj = json_decode($data);
        $first_pi = $obj->prev[0]->intent_id ?? $obj->intent_id ?? '';
        return Response::json([
            'status' => 'success',
            'data' => [
                'id' => 'id_' . unpack('H*', "$product_id:$first_pi")[1],
                'gateway' => $obj->gateway
            ]
        ]);
    }

    public function intent(Request $request)
    {
        $data = base64_decode($request->query('data'));
        $product_id = $request->query('id');

        $data = cryptoJsAesDecrypt(env('AES_HTTP_SECRET'), $data);
        if (empty($data)) return Response::json(['status' => 'error', 'code' => 1]);

        $obj = json_decode($data);
        $testmode_key = $obj->testmode_key;
        $gateway = $obj->gateway;

        $order_id = $obj->platform->order_id;
        $order = Order::find($order_id);
        if (empty($order)) return Response::json(['status' => 'error', 'code' => 2]);

        $customer = Customer::find($order->customer_id);
        if (empty($customer)) return Response::json(['status' => 'error', 'code' => 3]);

        $product = Product::find($product_id);
        if (empty($product)) return Response::json(['status' => 'error', 'code' => 4]);

        $lang = $product->language->code ?? 'en_US';
        $__ = json_decode(join("\n", file(base_path("lang/" . $lang . ".json"))));

        $total = $product->price_promo ?: $product->price;
        $total_currency = number_to_currency_by_symbol($total, $product->currency_symbol);
        $symbol = currency_code_to_symbol($product->currency_symbol)->value;
        $cycle = $product->payment_type === 'recurring' ? get_recurrence_interval($product->recurrence_interval, $product->recurrence_interval_count) : '';

        $payment_method = '';

        if ($gateway === 'stripe')
        {
            $stripe_conf = [
                'api_key' => stripe_secret($testmode_key),
                'stripe_version' => '2023-10-16',
            ];

            // esta utilizando de forma fixa esta conta connect
            if (env('STRIPE_CONNECT') == 'true' && env('STRIPE_CONNECT_ACCOUNT')) $stripe_conf['stripe_account'] = env('STRIPE_CONNECT_ACCOUNT');

            $stripe = new \Stripe\StripeClient($stripe_conf);

            $prev_intent_id = $obj->intent_id;
            $payment_intent = $stripe->paymentIntents->retrieve($prev_intent_id);
            $payment_method = $payment_intent->payment_method ? $stripe->paymentMethods->retrieve($payment_intent->payment_method) : null;
        }

        else if ($gateway === 'iugu')
        {
            $card = aes_decode_db($obj->card->token);
            $regexp = '/([0-9]+)(?:\s)([0-9]{2})(?:\/)([0-9]{2})(?:\s)([0-9]{3,4})/';
            $cc = preg_replace($regexp, '$1', $card);
            $last4 = substr(preg_replace($regexp, '$1', $card), -4);
            $exp_month = preg_replace($regexp, '$2', $card);
            $exp_year = preg_replace($regexp, '$3', $card);
            $brand = bincheck($cc);

            $payment_method = [
                'billing_details' => [
                    'name' => $customer->name,
                    'email' => $customer->email
                ],
                'card' => [
                    'brand' => $brand,
                    'last4' => $last4,
                    'exp_month' => $exp_month,
                    'exp_year' => $exp_year,
                ]
            ];
        }

        return Response::json([
            'status' => 'success',
            'data' => [
                'paymentMethod' => $payment_method,
                'product' => $product,
                'total' => $total,
                'total_currency' => "$symbol $total_currency",
                'cycle' => $cycle,
                '__' => $__
            ]
        ]);
    }

    public function min_css(Request $request)
    {
        header("Content-Type: text/css");
        return View::cssmin('frontend/pcss/public/upsell/upsellv2.css.php');
    }

    public function min_js(Request $request)
    {
        header("Content-Type: application/javascript");
        return View::jsmin('frontend/pjs/public/upsell/upsellv2.js.php');
    }

    public function pay(Request $request)
    {
        $descriptor = env('IUGU_DESCRIPTOR');
        $data = base64_decode($request->query('data'));
        $product_id = $request->query('id');
        $product = Product::where('id', $product_id)->first();
        $defaultCheckout = $product->defaultCheckout();

        $currency_quote = $product->currency == 'brl' ? 1 : (float) get_setting(($product->currency ?: 'usd') . '_brl');
        if (empty($currency_quote)) return Response::json(['status' => 'error', 'message' => 'Erro ao converter moeda.'], '400 Bad Request');

        $price = $product->price_promo ?: $product->price;
        $price_int = intval($price * 100);

        $data = cryptoJsAesDecrypt(env('AES_HTTP_SECRET'), $data);

        $obj = json_decode($data);
        if (empty($obj)) return Response::json(['status' => 'error', 'message' => 'Nenhum pagamento anterior encontrado.'], '400 Bad Request');

        $testmode_key = $obj->testmode_key;
        $gateway = $obj->gateway;

        $prev_customer_id = $obj->platform->customer_id;
        $prev_order_id = $obj->platform->order_id;

        // $error_prev_pi = $_SESSION['error_prev_pi'] ?? [];
        // if (count($error_prev_pi) && in_array($error_prev_code, $error_prev_pi))
        //     return Response::json(['status' => 'error', 'message' => 'Erro no pagamento, tente outro cartão.'], '400 Bad Request');

        $customer = Customer::find($prev_customer_id);
        $prev_order = Order::find($prev_order_id);
        // $prev_checkout = Checkout::find($prev_order->checkout_id);
        // $prev_product = Product::find($prev_checkout->product_id);

        $current_order_total = $price;
        $current_order_total_int = $price_int;
        $total_int = intval($current_order_total_int * $currency_quote); // converte o total para reais
        $total = intval($total_int) / 100;

        $order = new Order;
        $order->lang = $product->language->code ?? '';
        $order->status = EOrderStatus::PENDING; // deixe para o webhook dizer se foi aprovado ou nao
        $order->status_details = EOrderStatusDetail::PENDING;
        $order->gateway = $gateway;
        $order->uuid = uuid();

        // if (!$transaction_id) return Response::json(['status' => 'error', 'message' => 'Erro ao fazer o pagamento.']);

        $gateway_fee_percent = doubleval(get_setting('gateway_fee_percent')) / 100;
        $gateway_fee_price = doubleval(get_setting('gateway_fee_price'));

        $transaction_fee_extra = doubleval(get_setting('transaction_fee_extra'));
        $platform_fee = doubleval(get_setting('transaction_fee')) / 100;

        $total_gateway = $total * $gateway_fee_percent + $gateway_fee_price;
        $total_seller = $total - $total * $platform_fee - $transaction_fee_extra;
        $total_vendor = $total - $total_seller - $total_gateway;

        // $gateway_fee_percent = doubleval(get_setting('gateway_fee_percent')) / 100;
        // $gateway_fee_price = doubleval(get_setting('gateway_fee_price'));

        // $platform_fee = doubleval(get_setting('transaction_fee')) / 100;
        // $total_gateway = $total * $gateway_fee_percent + $gateway_fee_price;
        // $total_seller = $total - $total * $platform_fee;
        // $total_vendor = $total - $total_seller - $total_gateway;

        $order->user_id = $customer->user_id;
        $order->total = $total;
        $order->total_seller = $total_seller;
        $order->total_vendor = $total_vendor;
        $order->total_gateway = $total_gateway;
        $order->checkout_id = $defaultCheckout->id; // checkout padrao do produto atual
        // $order->status = $paid ? EOrderStatus::APPROVED : EOrderStatus::PENDING;
        // $order->transaction_id = $transaction_id;
        $order->customer_id = $customer->id;
        $order->currency_symbol = $product->currency;
        $order->currency_total = $price;
        $order->save();

        add_ordermeta($order->id, "customer_name", $customer->name);
        add_ordermeta($order->id, "customer_email", $customer->email);

        add_ordermeta($order->id, 'product_id', $product->id);
        add_ordermeta($order->id, 'product_price', $product->price_promo ?: $product->price);
        add_ordermeta($order->id, 'info_total', $product->price_promo ?: $product->price);


        $paid = false;
        $transaction_id = '';
        $payment_intent = null;

        // $prev_intent_client_secret = $obj->client_secret ?? '';
        $prev_intent_id = $obj->intent_id ?? '';
        $first_intent_id = $obj->prev[0]->intent_id ?? $prev_intent_id ?? '';
        $error_prev_code = $first_intent_id ? "$product->id:$first_intent_id" : "";

        if ($gateway === 'stripe')
        {
            $stripe_conf = [
                'api_key' => stripe_secret($testmode_key),
                'stripe_version' => '2023-10-16',
            ];

            if (env('STRIPE_CONNECT') == 'true' && env('STRIPE_CONNECT_ACCOUNT')) $stripe_conf['stripe_account'] = env('STRIPE_CONNECT_ACCOUNT');

            $stripe = new \Stripe\StripeClient($stripe_conf);

            $prev_payment_intent = $stripe->paymentIntents->retrieve($prev_intent_id);

            if ($prev_payment_intent->payment_method)
            {
                if ($product->payment_type === EProductPaymentType::RECURRING->value)
                {
                    // criar a assinatura aqui, tirar completamente do webhook
                    // entao o checkout inicial nao vai ter recorrencia

                    // $start_timestamp = strtotime(today() . " + $product->recurrence_interval_count $product->recurrence_interval");

                    $subscription = new Subscription;
                    $subscription->status = ESubscriptionStatus::PENDING;
                    $subscription->customer_id = $customer->id;
                    $subscription->order_id = $order->id;
                    $subscription->interval = $product->recurrence_interval;
                    $subscription->interval_count = $product->recurrence_interval_count;
                    // $subscription->expires_at = date("Y-m-d H:i:s", strtotime(today() . " + $subscription->interval_count $subscription->interval"));
                    $subscription->save();

                    $price = $stripe->prices->create([
                        'currency' => $order->currency_symbol,
                        'unit_amount' => intval($order->currency_total * 100),
                        'recurring' => [
                            'interval' => $product->recurrence_interval,
                            'interval_count' => $product->recurrence_interval_count,
                        ],
                        'product' => check_testmode_key($testmode_key) ? env('STRIPE_PRODUCT') : $product->gateway_product_id
                    ]);

                    $stripe_subscription = null;

                    // nao cobra na hora, cobra somente no proximo ciclo, a primeira cobranca eh via payment_intent
                    try
                    {
                        // obrigatorio que o customer tenha um metodo de pagamento anexado
                        $stripe_subscription = $stripe->subscriptions->create([
                            'customer' => $prev_payment_intent->customer,
                            'items' => [['price' => $price->id]],
                            'payment_behavior' => 'default_incomplete',
                            'default_payment_method' => $prev_payment_intent->payment_method,
                        ]);

                        $order->gateway_subscription_id = $stripe_subscription->id ?? '';
                        // $order->skip_invoice_paid = 1;
                    }
                    catch (Exception $ex)
                    {
                        // echo $ex->getMessage();
                        // die();
                    }

                    try
                    {
                        $payment_intent = $stripe->paymentIntents->create([
                            'amount' => $price_int,
                            'currency' => $product->currency,
                            'automatic_payment_methods' => ['enabled' => true],
                            'customer' => $prev_payment_intent->customer,
                            'payment_method' => $prev_payment_intent->payment_method,
                            'return_url' => site_url() . "/thanks2",
                            'off_session' => true,
                            'confirm' => true,
                            'metadata' => ['order_id' => $order->uuid],
                            'statement_descriptor_suffix' => 'UPSELL'
                        ]);

                        $intent_client_secret = $payment_intent->client_secret ?? '';
                        $transaction_id = $payment_intent->id ?? '';
                        $paid = $payment_intent->status == 'succeeded';

                        $order->transaction_id = $transaction_id;

                        $stripe_payment_intent = new StripePaymentIntent;
                        $stripe_payment_intent->payment_intent = $transaction_id;
                        $stripe_payment_intent->order_id = $order->id;
                        $stripe_payment_intent->status = EStripePaymentIntentStatus::CREATED;
                        $stripe_payment_intent->save();
                    }
                    catch (\Stripe\Exception\CardException $e)
                    {
                    }

                    $order->save();

                    // cria nova fatura
                    $invoice = new Invoice;
                    $invoice->order_id = $order->id;
                    $invoice->due_date = date("Y-m-d H:i:s", strtotime(today() . " + $subscription->interval_count $subscription->interval"));
                    $invoice->paid = false;
                    $invoice->save();

                    if (($stripe_subscription->status ?? '') == 'active')
                    {
                        $paid = true;
                    }

                    // if (($stripe_subscription->status ?? '') == 'active')
                    // {
                    //     $paid = true;

                    //     // marca fatura atual como paga
                    //     $invoice = new Invoice;
                    //     $invoice->order_id = $order->id;
                    //     $invoice->paid_at = today();
                    //     $invoice->paid = true;
                    //     $invoice->save();

                    //     $subscription->status = ESubscriptionStatus::ACTIVE;
                    //     $subscription->expires_at = date("Y-m-d H:i:s", strtotime(today()." + $subscription->interval_count $subscription->interval"));
                    //     $subscription->save();
                    // }
                }
                else
                {
                    try
                    {
                        $payment_intent = $stripe->paymentIntents->create([
                            'amount' => $price_int,
                            'currency' => $product->currency,
                            'automatic_payment_methods' => ['enabled' => true],
                            'customer' => $prev_payment_intent->customer,
                            'payment_method' => $prev_payment_intent->payment_method,
                            'return_url' => site_url() . "/thanks2",
                            'off_session' => true,
                            'confirm' => true,
                            'metadata' => ['order_id' => $order->uuid],
                            'statement_descriptor_suffix' => 'UPSELL'
                        ]);

                        $intent_client_secret = $payment_intent->client_secret ?? '';
                        $transaction_id = $payment_intent->id ?? '';
                        $paid = $payment_intent->status == 'succeeded';

                        $order->transaction_id = $transaction_id;
                        $order->save();

                        $stripe_payment_intent = new StripePaymentIntent;
                        $stripe_payment_intent->payment_intent = $transaction_id;
                        $stripe_payment_intent->order_id = $order->id;
                        $stripe_payment_intent->status = EStripePaymentIntentStatus::CREATED;
                        $stripe_payment_intent->save();
                    }
                    catch (\Stripe\Exception\CardException $e)
                    {
                        // Error code will be authentication_required if authentication is needed
                        // echo 'Error code is:' . $e->getError()->code;
                        // $payment_intent_id = $e->getError()->payment_intent->id;
                        // $payment_intent = $stripe->paymentIntents->retrieve($payment_intent_id);
                        // die();
                    }
                }
            }
        }

        if ($gateway === 'iugu')
        {
            $card = aes_decode_db($obj->card->token);
            $regexp_card = '/([0-9]+)(?:\s)([0-9]{2})(?:\/)([0-9]{2})(?:\s)([0-9]{3,4})/';
            $cc = preg_replace($regexp_card, '$1', $card);
            $exp_month = preg_replace($regexp_card, '$2', $card);
            $exp_year = preg_replace($regexp_card, '$3', $card);
            $cvc = preg_replace($regexp_card, '$4', $card);
            $name = $customer->name;
            $email = $customer->email;
            $first_name = preg_replace('/([\w]+)\s([\w\s]+)/', '$1', $name);
            $last_name = preg_replace('/([\w]+)\s([\w\s]+)/', '$2', $name);

            $test = env('IUGU_TEST') ? 'true' : 'false';
        
            $id_date = date('Ymd');
            $id_card = substr($cc, 0, 6) . substr($cc, -4);
            $id_price = preg_replace('/\D/', '', $order->total);

            $order->alert_id = "$id_price-$id_date-$id_card";
            $order->save();

            $iugu_seller = IuguSeller::where('user_id', $product->user_id)->orderBy('id', 'DESC')->first();
            $account_id = ($iugu_seller->account_id ?? '') ?: env('IUGU_ACCOUNT_ID');
            $iugu_token = ($iugu_seller->live_api_token ?? '') ?: env('IUGU_API_TOKEN');
            if ($test == 'true') $account_id = env('IUGU_ACCOUNT_ID');

            $payload_token = [
                "account_id" => env('IUGU_ACCOUNT_ID'),
                "method" => "credit_card",
                "test" => $test,
                "data" => [
                    "number" => $cc,
                    "verification_value" => $cvc,
                    "first_name" => $first_name,
                    "last_name" => $last_name,
                    "month" => $exp_month,
                    "year" => $exp_year
                ]
            ];

            $headers = ['Content-Type' => 'application/json'];

            $response = IuguRest::request(
                verb: 'POST',
                url: '/payment_token',
                headers: $headers,
                body: json_encode($payload_token),
                timeout: 3
            );

            $token = $response->json->id ?? '';

            $payload_charge = [
                "token" => $token,
                "keep_dunning" => true,
                "items" => [
                    "description" => $product->name,
                    "quantity" => 1,
                    "price_cents" => $total_int
                ],
                "payer" => [
                    "name" => $name,
                    "email" => $email,
                    "order_id" => $order->id,
                    "soft_descriptor_light" => $descriptor
                ]
            ];

            $response = IuguRest::request(
                verb: 'POST',
                url: '/charge?api_token=' . $iugu_token,
                headers: $headers,
                body: json_encode($payload_charge),
                timeout: 30
            );
            
            $json = $response->json;
            
            $response_verb = $response->verb;
            $response_url = $response->url;
            $response_errno = $response->errno;
            $response_error = $response->error;
            $response_status_code = $response->status_code;
            $response_time = $response->time;
            $response_body = $response->body;
            $now = today();

            $order->log = json_encode($response);

            $paid = $json->status === 'captured';
            $order->transaction_id = $json->invoice_id ?? '';
            $order->save();

            if ($product->payment_type === EProductPaymentType::RECURRING->value)
            {
                $subscription = new Subscription;
                $subscription->status = ESubscriptionStatus::PENDING;
                $subscription->customer_id = $customer->id;
                $subscription->order_id = $order->id;
                $subscription->interval = $product->recurrence_interval;
                $subscription->interval_count = $product->recurrence_interval_count;
                $subscription->save();

                $subscription_expires_at = date("Y-m-d H:i:s", strtotime(today() . " + $subscription->interval_count $subscription->interval"));

                $invoice = new Invoice;
                $invoice->order_id = $order->id;
                $invoice->due_date = $subscription_expires_at;
                $invoice->paid = false;
                $invoice->save();

                IuguChargeQueue::push(
                    new IuguChargeQueueDataList([
                        'token' => [
                            'verb' => 'POST',
                            'uri' => '/payment_token',
                            'headers' => $headers,
                            'query_string' => null,
                            'payload' => $payload_token
                        ],
                        'charge' => [
                            'verb' => 'POST',
                            'uri' => '/charge?api_token=' . $iugu_token,
                            'headers' => $headers,
                            'query_string' => null,
                            'payload' => $payload_charge
                        ],
                        'meta' => [
                            'order_id' => $order->id,
                            'subscription_id' => $subscription->id,
                            'invoice_id' => $invoice->id
                        ]
                    ]), 
                    $subscription_expires_at
                );
            }
        }

        $url = $product->has_upsell ? $product->upsell_link : '';
        $thanks_url = get_subdomain_serialized('checkout') . "/thanks?id=$order->uuid";
        $thanks_url_first_order = get_subdomain_serialized('checkout') . "/thanks?id=$prev_order->uuid";

        $payment_intent_id = $payment_intent->id ?? null;
        $current_id = "$product->id:$payment_intent_id";
        $payment_intent_client_secret = $payment_intent->client_secret ?? null;

        $obj2 = (object) [
            "timestamp" => time(),
            "gateway" => $obj->gateway,
            "intent_id" => $payment_intent_id,
            "client_secret" => $payment_intent_client_secret,
            "customer" => $customer->id,
            "platform" => (object) [
                "order_id" => $order->id,
                "customer_id" => $customer->id,
            ],
            "paid" => $paid,
            "prev" => count($obj->prev ?? []) ? array_merge($obj->prev, [$obj]) : [$obj],
            "testmode_key" => $testmode_key,
        ];

        $data = base64_encode(cryptoJsAesEncrypt(env('AES_HTTP_SECRET'), json_encode($obj2)));

        return Response::json([
            'status' => 'success',
            'message' => $paid ? 'Compra aprovada.' : 'Erro no pagamento.',
            'url' => $url,
            'thanks_url' => $paid ? $thanks_url : $thanks_url_first_order,
            'paid' => $paid,
            'data' => $data,
            'first_id' => 'id_' . unpack('H*', $error_prev_code)[1],
            'current_id' => 'id_' . unpack('H*', $current_id)[1]
        ]);
    }

    public function clear(Request $request)
    {
        unset($_SESSION['payment_intent_with_error']);

        return Response::json(
            new ResponseData(['status' => EResponseDataStatus::SUCCESS, 'message' => 'Esta seção foi limpa com sucesso.']),
            new ResponseStatus('200 OK')
        );
    }
}
