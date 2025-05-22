<?php

namespace Backend\Controllers\Subdomains\Checkout;

use Backend\App;
use Backend\Enums\Customer\ECustomerStatus;
use Backend\Enums\Order\EOrderStatus;
use Backend\Enums\Order\EOrderStatusDetail;
use Backend\Enums\Pixel\EPixelPlatform;
use Backend\Enums\Product\EProductPaymentType;
use Backend\Enums\Subscription\ESubscriptionStatus;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Models\Balance;
use Backend\Models\Customer;
use Backend\Models\Invoice;
use Backend\Models\Pixel;
use Backend\Models\StripePaymentIntent;
use Backend\Models\Subscription;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Product;
use Backend\Models\Order;
use Backend\Models\Checkout;
use Backend\Exceptions\Product\ProductNotFoundException;
use Backend\Controllers\Browser\NotFoundController;
use Backend\Types\Stripe\Entity\EStripePaymentIntentStatus;
use Exception;
use Ezeksoft\PHPWriteLog\Log;

class ReturnUrlController
{
    public App $application;

    public string $title = 'Checkout';
    public string $context = 'public';
    public string $subdomain = 'checkout';
    public ?User $user;
    public ?string $domain;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->user = user();
        $this->domain = get_subdomain_serialized($this->subdomain);
    }

    public function error($code)
    {
        return Response::redirect("/error?code=$code");
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        $payment_intent_param = $request->query('payment_intent');
        $store_user_id = $request->query('store_user_id');
        $checkout_id = $request->query('checkout_id');
        $name = $request->query('name');
        $email = $request->query('email');
        $country = $request->query('country');
        $product_id = $request->query('product_id');
        $testmode_key = $request->query('_b');

        $product = Product::find($product_id);
        if (empty($product)) return $this->error(1);

        $stripe_conf = [
            'api_key' => stripe_secret($testmode_key),
            'stripe_version' => '2023-10-16',
        ];

        if (env('STRIPE_CONNECT') == 'true' && env('STRIPE_CONNECT_ACCOUNT')) $stripe_conf['stripe_account'] = env('STRIPE_CONNECT_ACCOUNT');

        $stripe = new \Stripe\StripeClient($stripe_conf);

        $currency_quote = $product->currency == 'brl' ? 1 : (float) get_setting(($product->currency ?: 'usd') . '_brl');
        if ($currency_quote <= 0) return $this->error(2);

        $payment_intent = $stripe->paymentIntents->retrieve($payment_intent_param);
        
        $paid = $payment_intent->status == 'succeeded';

        $stripe_customer = $stripe->customers->update($payment_intent->customer, [
            "email" => $email,
            "name" => $name,
        ]);

        $payment_intent_total_int = $total_int = $payment_intent->amount ?? 0; // total em inteiro
        $payment_intent_total = $payment_intent_total_int / 100; // total em decimal

        if ($total_int <= 0) return $this->error(3);
        $total_int = intval($total_int * $currency_quote);

        // total em decimal
        $total = intval($total_int) / 100;

        $gateway_fee_percent = doubleval(get_setting('gateway_fee_percent')) / 100;
        $gateway_fee_price = doubleval(get_setting('gateway_fee_price'));

        $transaction_fee_extra = doubleval(get_setting('transaction_fee_extra'));
        $platform_fee = doubleval(get_setting('transaction_fee')) / 100;

        $total_gateway = ($total * $gateway_fee_percent) + $gateway_fee_price;
        $total_seller = $total - ($total * $platform_fee) - $transaction_fee_extra;
        $total_vendor = $total - $total_seller - $total_gateway;
        
        // TODO: script para atualizar o transaction_fee_extra no valor de 1 dolar

        $customer = Customer::where('email', $email)->first();
        if (empty($customer))
        {
            $customer = new Customer;
            $customer->name = $name;
            $customer->email = $email;
            $customer->user_id = $store_user_id;
            $customer->password = null;
            $customer->access_token = ghash();
            $customer->one_time_access_token = $customer->access_token;
            $customer->status = ECustomerStatus::ACTIVE->value;
            $customer->address_country = $country;
            $customer->user_agent = $_SERVER['HTTP_USER_AGENT'];
            $customer->ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'];
            $customer->save();
        }

        // $order = new Order;
        $current_order_id = $_SESSION['current_order'];
        $order = Order::find($current_order_id);
        if (empty($order)) return $this->error(4);
        $order->user_id = $store_user_id;
        $order->total = $total;
        $order->total_seller = $total_seller;
        $order->total_vendor = $total_vendor;
        $order->total_gateway = $total_gateway;
        $order->checkout_id = $checkout_id;
        $order->transaction_id = $payment_intent_param; // tornar esse campo unico
        $order->customer_id = $customer->id;
        $order->currency_symbol = $product->currency;
        $order->currency_total = $payment_intent_total;
        $order->status = EOrderStatus::PENDING;
        $order->status_details = EOrderStatusDetail::PENDING;
        $order->gateway = 'stripe';
        $order->save();

        $stripe_payment_intent = new StripePaymentIntent;
        $stripe_payment_intent->payment_intent = $payment_intent_param;
        $stripe_payment_intent->order_id = $order->id;
        $stripe_payment_intent->status = EStripePaymentIntentStatus::CREATED;
        $stripe_payment_intent->save();

        add_ordermeta($order->id, "customer_name", $customer->name);
        add_ordermeta($order->id, "customer_email", $customer->email);

        add_ordermeta($order->id, 'product_id', $product->id);
        add_ordermeta($order->id, 'product_price', $product->price_promo ?: $product->price);
        add_ordermeta($order->id, 'info_total', $product->price_promo ?: $product->price);

        if ($product->payment_type === EProductPaymentType::RECURRING->value)
        {
            $subscription = new Subscription;
            $subscription->status = ESubscriptionStatus::PENDING;
            $subscription->customer_id = $customer->id;
            $subscription->order_id = $order->id;
            $subscription->interval = $product->recurrence_interval;
            $subscription->interval_count = $product->recurrence_interval_count;
            $subscription->save();

            $price = $stripe->prices->create([
                'currency' => $product->currency,
                'unit_amount' => intval($order->currency_total * 100),
                'recurring' => [
                    'interval' => $product->recurrence_interval,
                    'interval_count' => $product->recurrence_interval_count,
                ],
                'product' => $testmode_key ? 'prod_Q8chK0A6n9pjmD' : $product->gateway_product_id, // gera um erro caso nao seja passado um id de produto de testes no modo teste
            ]);

            $start_timestamp = strtotime(today() . " + $product->recurrence_interval_count $product->recurrence_interval");

            $stripe_subscription = null;

            try
            {
                // obrigatorio que o customer tenha um metodo de pagamento anexado
                $obj = [
                    'customer' => $payment_intent->customer,
                    'items' => [['price' => $price->id]],
                    // 'billing_cycle_anchor' => $start_timestamp,
                    'payment_behavior' => 'default_incomplete',
                    'default_payment_method' => $payment_intent->payment_method,
                    // 'metadata' => ['order_id' => $order->uuid]
                ];

                $stripe_subscription = $stripe->subscriptions->create($obj);

                $order->gateway_subscription_id = $stripe_subscription->id;
                // $order->skip_invoice_paid = 1;
                $order->save();

                // (new Log)->write(
                //     base_path("logs/stripe_subscription.txt"), 
                //     "\n\n\n[".today()."] ".
                //     json_encode($stripe_subscription)
                // );
            }
            catch (Exception $ex)
            {
                // print_r($ex->getMessage());
                // die();
            }

            $invoice = new Invoice;
            $invoice->order_id = $order->id;
            $invoice->due_date = date("Y-m-d H:i:s", strtotime(today() . " + $product->recurrence_interval_count $product->recurrence_interval"));
            $invoice->paid = false;
            $invoice->save();
        }

        $data = '';

        if ($payment_intent->id)
        {
            $data = json_encode([
                "timestamp" => time(),
                "gateway" => "stripe",
                "intent_id" => $payment_intent->id,
                "client_secret" => $payment_intent->client_secret,
                "customer" => $customer->id,
                "platform" => (object) [
                    "order_id" => $order->id,
                    "customer_id" => $customer->id,
                ],
                "paid" => $paid,
                "testmode_key" => $testmode_key,
            ]);

            $data = base64_encode(cryptoJsAesEncrypt(env('AES_HTTP_SECRET'), $data));
        }

        $sep = $product->has_upsell ? (str_contains($product->upsell_link, "?") ? "&" : "?") : "?";

        // return Response::redirect();
        $redirect_url = $product->has_upsell ? $product->upsell_link . $sep . "data=$data" : get_subdomain_serialized("checkout") . "/thanks?id=$order->uuid";

        // header("location: $redirect_url");

        $pixels = Pixel::where('product_id', $product->id)->where('platform', EPixelPlatform::FACEBOOK->value)->orderBy('id', 'DESC')->first();
        $facebook_pixel = ($pixels ?? null)->content ?? '';

        // if ($facebook_pixel) file_get_contents("https://www.facebook.com/tr?id=$facebook_pixel&ev=Purchase&cd[currency]="
        //     .(strtoupper($order->currency_symbol))."&cd[value]=$order->currency_total");
        $purchase_obj = ["currency" => strtoupper($product->currency), "value" => (float) number_format($payment_intent_total, 2)];

        if ($facebook_pixel)
        {
?>
<script>
! function(f, b, e, v, n, t, s) {
    if (f.fbq) return;
    n = f.fbq = function() {
        n.callMethod ?
            n.callMethod.apply(n, arguments) : n.queue.push(arguments)
    };
    if (!f._fbq) f._fbq = n;
    n.push = n;
    n.loaded = !0;
    n.version = '2.0';
    n.queue = [];
    t = b.createElement(e);
    t.async = !0;
    t.src = v;
    s = b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t, s)
}(window, document, 'script',
    'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '<?= $facebook_pixel ?>');
fbq('track', 'Purchase', <?= json_encode($purchase_obj) ?>);
</script>
<?php
        }
        ?>
<script>
setTimeout(() => location.href = '<?= $redirect_url ?>', 5000)
</script>
<style>
* {
    padding: 0;
    margin: 0
}

html {
    background-color: #c9d2d9;
}

.loader {
    width: 50px;
    padding: 4px;
    aspect-ratio: 1;
    border-radius: 50%;
    background: #5783a6;
    --_m:
        conic-gradient(#0000 10%, #000),
        linear-gradient(#000 0 0) content-box;
    -webkit-mask: var(--_m);
    mask: var(--_m);
    -webkit-mask-composite: source-out;
    mask-composite: subtract;
    animation: l3 1s infinite linear;
}

@keyframes l3 {
    to {
        transform: rotate(1turn)
    }
}

.wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
</style>
<div class="wrapper">
    <div class="loader"></div>
</div>
<?php
    }
}