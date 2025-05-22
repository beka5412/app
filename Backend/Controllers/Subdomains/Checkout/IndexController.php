<?php

namespace Backend\Controllers\Subdomains\Checkout;

use Backend\App;
use Backend\Enums\Order\EOrderStatus;
use Backend\Enums\Order\EOrderStatusDetail;
use Backend\Exceptions\Checkout\CheckoutNotFoundException;
use Backend\Http\Request;
use Backend\Models\Customer;
use Backend\Models\Order;
use Backend\Template\View;
use Backend\Exceptions\Product\ProductNotFoundException;
use Backend\Exceptions\Checkout\CheckoutDisabledException;
use Backend\Controllers\Browser\NotFoundController;
use Backend\Enums\Checkout\ECheckoutStatus;
use Backend\Enums\Product\EProductType;
use Backend\Http\Response;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;
use Setono\MetaConversionsApi\Event\Event;
use Setono\MetaConversionsApi\Pixel\Pixel as FacebookPixel;
use Setono\MetaConversionsApi\Client\Client;
use Backend\Models\User;
use Backend\Models\Product;
use Backend\Models\Purchase;
use Backend\Models\Checkout;
use Backend\Models\ProductLink;
use Backend\Models\Pixel;
use Backend\Models\Domain;
use Backend\Models\Orderbump;
use Backend\Models\Upsell;
use Backend\Models\Plan;
use Backend\Models\Setting;
use Backend\Types\Response\EResponseDataStatus;
use Ezeksoft\RocketZap\SDK as RocketZap;
use Ezeksoft\RocketZap\Enum\ProjectType;

class IndexController
{
    public App $application;
    public string $title = 'Checkout';
    public string $context = 'public';
    public string $indexFile = 'frontend/view/subdomains/checkout/indexView.php';
    public string $subdomain = 'checkout';
    public string $domain;
    public ?User $user;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->user = user();
        $this->subdomain = 'checkout';
        $this->domain = get_subdomain_serialized($this->subdomain);
    }

    public function index(Request $request)
    {
        $initial_sku = $sku = substr($request->uri(), 1);
        $aff_sku = $request->query('aff');
        $_a = $request->query('_a'); // payment intent id
        $_b = $testmode_key = $request->query('_b'); // test mode authentication
        $_c = $request->query('_c'); // use stripe test mode
        $_gateway = $request->query('gateway') ?: '1';
        $gateway_selected = 'iugu';
        if ($_gateway == 2) $gateway_selected = 'stripe';

        // schema:
        // {protocol}://{subdomain}.{domain}/{sku}/{variation}

        $uri_aux = explode("?", $sku);
        $sku = $uri_aux[0];
        $initial_sku = $uri_aux[0];
        $query_string = $uri_aux[1] ?? '';

        if (substr($sku, -1) == '/')
            $sku = substr($sku, 0, -1);

        $variation = '';
        $aux = explode("/", $sku);
        if (sizeof($aux) == 1)
        {
            $sku = $aux[0];
        }
        else if (sizeof($aux) == 2)
        {
            $sku = $aux[0];
            $variation = $aux[1];
        }

        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        $plan = Plan::where('slug', $sku)->first();

        if (empty($plan))
            $checkout = Checkout::where('sku', $sku)->with('theme')->with('testimonials')->first();
        else
            $checkout = $variation // a variation seria o sku do checkout
                ? Checkout::where('sku', $variation)->with('theme')->first()
                : Checkout::where('default', 1)->with('theme')->first();

        // permite mostrar uma tela utilizando o sku do produto ao inves de um checkout
        // if (!empty($checkout)) $sku = $checkout->product->sku;

        try
        {
            if (empty($checkout))
                throw new CheckoutNotFoundException;

            if ($checkout?->status == ECheckoutStatus::DRAFT->value || $checkout?->status == ECheckoutStatus::DISABLED->value)
                throw new CheckoutDisabledException;

            $product = Product::where('id', $checkout->product_id)
                //where('sku', $sku)
                ->with([
                    'orderbumps' => function ($query)
                    {
                        $query->with('product', 'product_as_checkout');
                    }
                ])
                ->with('language')
                ->first();

            $seller = User::find($product->user_id);

            // if (!$seller->kyc_confirmed || $seller->account_under_analysis || !$product->approved)
            // {
            //     $notfound = new NotFoundController($this->application);
            //     return $notfound->element(new Request);
            // }

            if ($aff_sku)
            {
                $cookie_duration = ((int) $product->cookie_duration > 0 ? $product->cookie_duration : 50 * 365 * 24 * 60 * 60);
                setcookie('cookie_last_click', $aff_sku, time() + $cookie_duration);
                if (empty($_COOKIE['cookie_first_click']))
                    setcookie('cookie_first_click', $aff_sku, time() + $cookie_duration);
            }

            // if (empty($sku))
            // {
            //     // echo get_subdomain_serialized(subdomain());
            //     $aux = explode("://", current_url());
            //     $domain_str = $aux[1] ?? '';

            //     $domain = Domain::where('domain', $domain_str)->first();
            //     if (empty($domain))
            //         return;

            //     $domain_id = $domain->id ?? '';

            //     $pixels_ = Pixel::where('domain_id', $domain_id)->get();
            //     $pixels = [];
            //     foreach ($pixels_ as $pixel)
            //     {
            //         unset($pixel["access_token"]);
            //         $pixels[] = $pixel;
            //     }

            //     return View::render("frontend/view/subdomains/checkout/welcomeView.php", compact('context', 'pixels'));
            // }

            if (empty($product))
                throw new ProductNotFoundException;

            $purchased_qty = Purchase::where('product_id', $product->id)->where('status', 'active')->count();
            $product_link = $variation ? ProductLink::where('slug', $variation)->where('product_id', $product->id)->first() : null;

            // $plan = $variation && $checkout?->product_id
            //     ? Plan::where('slug', $variation)->where('product_id', $checkout->product_id)->first()
            //     : null
            // ;

            $product_price = $total = $plan?->price ?: $product_link?->amount ?: $product->price_promo ?: $product->price;

            $pixels_ = Pixel::where('user_id', $product->user_id)->where('product_id', $product->id)->with('domain')->get();
            $pixels = [];
            foreach ($pixels_ as $pixel)
            {
                unset($pixel["access_token"]);
                $pixels[] = $pixel;
            }

            $sck = $request->query('sck');
            $src = $request->query('src');
            $utm_source = $request->query('utm_source');
            $utm_campaign = $request->query('utm_campaign');
            $utm_medium = $request->query('utm_medium');
            $utm_content = $request->query('utm_content');
            $utm_term = $request->query('utm_term');
            $xcod = $request->query('xcod');

            /*
            foreach ($pixels as $pixel)
            {
                // pixel do facebook
                if ($pixel->platform == "facebook" && $pixel->access_token)
                {
                    $event = new Event(Event::EVENT_INITIATE_CHECKOUT);
                    $event->pixels[] = new FacebookPixel($pixel->content, $pixel->access_token);

                    if (!empty($event->pixels))
                    {
                        $event->eventSourceUrl = $pixel->domain->domain ?: full_url();
                        // $event->userData->clientUserAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36';
                        // $event->userData->email[] = 'johndoe@example.com';
                        // $event->testEventCode = 'test event code'; // uncomment this if you want to send a test event
                        $client = new Client();
                        $client->sendEvent($event);
                    }
                }
            }
            */

            $upsell = Upsell::where('user_id', $product->user_id)->where('product_id', $product->id)->first();

            if (EProductType::PHYSICAL->value == $product->type)
                $total += $product->shipping_cost;

            // TODO: copiar implementacao para o metodo "element()"

            $locale = $product->language->code ?? '';


            // $customer = new Customer;
            // $customer->save();

            $current_order = new Order;
            $current_order->user_id = $product->user_id;
            $current_order->lang = $product->language->code ?? '';
            $current_order->uuid = uuid();
            $current_order->status = EOrderStatus::INITIATED;
            $current_order->status_details = EOrderStatusDetail::INITIATED;
            $current_order->save();
            $_SESSION['current_order'] = $current_order->id;

            add_ordermeta($current_order->id, 'tracking_sck', $sck);
            add_ordermeta($current_order->id, 'tracking_src', $src);
            add_ordermeta($current_order->id, 'tracking_utm_source', $utm_source);
            add_ordermeta($current_order->id, 'tracking_utm_campaign', $utm_campaign);
            add_ordermeta($current_order->id, 'tracking_utm_medium', $utm_medium);
            add_ordermeta($current_order->id, 'tracking_utm_content', $utm_content);
            add_ordermeta($current_order->id, 'tracking_utm_term', $utm_term);
            add_ordermeta($current_order->id, 'tracking_xcod', $xcod);
            add_ordermeta($current_order->id, 'user_agent', $_SERVER['HTTP_USER_AGENT']);
            add_ordermeta($current_order->id, 'ip', $_SERVER['REMOTE_ADDR']);

            $preview = (object) [
                'top_banner' => $request->query('top_banner'),
                'sidebar_banner' => $request->query('sidebar_banner'),
                'footer_banner' => $request->query('footer_banner'),
                'top_2_banner' => $request->query('top_2_banner'),
                'logo' => $request->query('logo'),
                'favicon' => $request->query('favicon'),
            ];

            View::render(
                $this->indexFile,
                compact(
                    'title',
                    'context',
                    'user',
                    'product',
                    'purchased_qty',
                    'total',
                    'checkout',
                    'product_link',
                    'variation',
                    'pixels',
                    'sku',
                    'initial_sku',
                    'upsell',
                    'product_link',
                    'product_price',
                    'plan',
                    // 'payment_intent',
                    'preview',
                    '_a',
                    '_b',
                    '_c',
                    'current_order',
                    'locale',
                    'gateway_selected'
                )
            );
        }
        catch (CheckoutNotFoundException $ex)
        {
            $notfound = new NotFoundController($this->application);
            $notfound->view($request);
        }
        catch (ProductNotFoundException $ex)
        {
            $notfound = new NotFoundController($this->application);
            $notfound->view($request);
        }
        catch (CheckoutDisabledException $ex)
        {
            // TODO: renderizar uma pagina referente a esta excecao
            $notfound = new NotFoundController($this->application);
            $notfound->view($request);
        }
    }

    /**
     * TODO: Este metodo está desatualizado
     */
    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        $params = $request->pageParams();
        $sku = $params->sku;

        $variation = '';
        $aux = explode("/", $sku);
        if (sizeof($aux) == 2)
        {
            $sku = $aux[0];
            $variation = $aux[1];
        }

        $checkout = Checkout::where('sku', $sku)->with('theme')->with('testimonials')->first();
        if (!empty($checkout))
            $sku = $checkout->product->sku;

        try
        {
            $product = Product::where('sku', $sku)
                ->with([
                    'orderbumps' => function ($query)
                    {
                        $query->with('product');
                    }
                ])
                ->with('language')
                ->first();
            if (empty($product))
                throw new ProductNotFoundException;

            $purchased_qty = Purchase::where('product_id', $product->id)->where('status', 'active')->count();
            $product_link = $variation ? ProductLink::where('slug', $variation)->where('product_id', $product->id)->first() : null;
            $product_price = $total = $product_link?->amount ?: $product->price_promo ?: $product->price;
            $pixels_ = Pixel::where('user_id', $product->user_id)->where('product_id', $product->id)->get();
            $pixels = [];
            foreach ($pixels_ as $pixel)
            {
                unset($pixel["access_token"]);
                $pixels[] = $pixel;
            }
            $upsell = Upsell::where('user_id', $product->user_id)->where('product_id', $product->id)->first();

            if (EProductType::PHYSICAL->value == $product->type)
                $total += $product->shipping_cost;

            View::response(
                $this->indexFile,
                compact(
                    'title',
                    'context',
                    'user',
                    'product',
                    'purchased_qty',
                    'total',
                    'checkout',
                    'product_link',
                    'variation',
                    'pixels',
                    'product_price'
                )
            );
        }
        catch (ProductNotFoundException $ex)
        {
            $notfound = new NotFoundController($this->application);
            $notfound->view($request);
        }
    }

    public function updateOrder(Request $request, $id)
    {
        $body = $request->json();
        $message = $body->message ?? '';

        $order = Order::where('id', $id)->where('status', EOrderStatus::INITIATED)->first();
        if (empty($order)) 
        {
            return Response::json(
                new ResponseData(['status' => EResponseDataStatus::ERROR, 'message' => 'Order not found.']),
                new ResponseStatus('200 OK')
            );
        }

        $order->status = EOrderStatus::CANCELED;
        $order->status_details = EOrderStatusDetail::REJECTED;
        $order->reason = $message;
        $order->save();

        return Response::json(
            new ResponseData(['status' => EResponseDataStatus::SUCCESS, 'message' => 'Order updated.']),
            new ResponseStatus('200 OK')
        );
    }

    public function paymentIntent(Request $request, $checkout_id)
    {
        [
            '_a' => $_a, // payment intent
            '_b' => $_b, // test mode key
            '_c' => $_c, // change stripe key to test mode
            'name' => $name,
            'email' => $email,
            'order_id' => $order_id,
            'orderbumps' => $orderbumps,
        ] = get_object_vars($request->json());

        $checkout = Checkout::find($checkout_id);
        if (!$checkout) return Response::json(['status' => 'error', 'code' => 1]);

        $product = $checkout->product;
        if (!$product) return Response::json(['status' => 'error', 'code' => 2]);

        $total = $product->price_promo ?: $product->price ?: 0;

        if (!empty($orderbumps) && gettype($orderbumps) === "array")
        {
            foreach ($orderbumps as $orderbump_info)
            {
                if ($orderbump_info->checked)
                {
                    $orderbump = Orderbump::where('id', $orderbump_info->orderbump_id)->first();

                    $total += $orderbump->price_promo ?: $orderbump->price;
                }
            }
        }

        $current_order = Order::find($order_id);
        if (!$current_order) return Response::json(['status' => 'error', 'code' => 3]);

        // \Stripe\Stripe::setAppInfo(
        //     "stripe-samples/accept-a-payment/payment-element",
        //     "0.0.2",
        //     "https://github.com/stripe-samples"
        // );

        $stripe_conf = [
            'api_key' => stripe_secret($_c == 1 ? $_b : ''),
            'stripe_version' => '2023-10-16',
            // 'stripe_account' => 'acct_1PfZInEQpvewJOpe'
        ];

        // esta utilizando de forma fixa esta conta connect
        if (env('STRIPE_CONNECT') == 'true' && env('STRIPE_CONNECT_ACCOUNT')) $stripe_conf['stripe_account'] = env('STRIPE_CONNECT_ACCOUNT');

        // TODO: utilizar a conta do vendedor ao invés
       
        // if ($has_seller)
        // $stripe_conf['stripe_account'] = check_testmode_key($_b) || env('STRIPE_SECRET') == env('STRIPE_SECRET_TEST') 
        //     ? 'acct_1PfZInEQpvewJOpe' : 'acct_1Pf2o9CsFYDMOhVy';

        $stripe = new \Stripe\StripeClient($stripe_conf);

        $payment_intent = null;

        $customer = $stripe->customers->create([
            'name' => $name,
            'email' => $email,
        ]);

        if ($_a)
        {
            // $payment_intent = $stripe->paymentIntents->retrieve($_a, [], ['stripe_account' => 'acct_1PfZInEQpvewJOpe']);
            $payment_intent = $stripe->paymentIntents->retrieve($_a); // , [], ['stripe_account' => 'acct_1PfZInEQpvewJOpe']
        }
        else
        {
            try
            {
                $payment_intent = $stripe->paymentIntents->create([
                    'automatic_payment_methods' => ['enabled' => true],
                    'amount' => intval($total * 100),
                    'currency' => $product->currency ?: 'usd',
                    'customer' => $customer->id,
                    "setup_future_usage" => 'off_session',
		    'metadata' => ['order_id' => $current_order->uuid],
		    //"payment_method_types" => [ "card" ],
                    // 'receipt_email' => $email,
                    // "receipt_email" // email para receber o recibo
                    // "statement_descriptor"
                    // "statement_descriptor_suffix"
                    // ["transfer_data" => ["destination" => ""]]
                ]
                // , [
                //     'stripe_account' => 'acct_1Pf2o9CsFYDMOhVy'
                // ]
            );
            }
            catch (\Stripe\Exception\ApiErrorException $e)
	    {
		   // print_r($e->getError()->message);
                // error_log($e->getError()->message);
            }
            catch (\Exception $e)
            {
                // error_log($e);
            }
	}


        return Response::json([
            'status' => 'success',
            'data' => $payment_intent,
            //'message' => $msg
        ]);
    }
}
