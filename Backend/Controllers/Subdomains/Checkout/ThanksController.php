<?php

namespace Backend\Controllers\Subdomains\Checkout;

use Backend\App;
use Backend\Http\Request;
use Backend\Template\View;
use Backend\Controllers\Browser\NotFoundController;
use Backend\Controllers\Controller\AFrontendController;
use Backend\Controllers\Controller\TFrontendController;
use Backend\Models\Order;
use Backend\Models\Pixel;
use Backend\Models\Customer;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ThanksController extends AFrontendController
{
    use TFrontendController;
    public string $title = 'Checkout';
    public string $context = 'public';
    public string $indexFile = 'frontend/view/subdomains/checkout/thanksView.php';
    public string $subdomain = 'checkout';

    public function view(string $view_method, Request $request, array $params = [], array $pagination = [])
    {
        extract((array) $this);
        extract($params);
        extract($pagination);
        $id = $request->query('id');

        $url = site_url() . '/thanks?' . $request->queryString();
        $view = new View;

        try
        {
            $order = Order::where('uuid', $id)->first();
            if (empty($order)) throw new ModelNotFoundException;

            $prev_intent_client_secret_obj = json_decode($_SESSION["intent_client_secret"] ?? '');
            $prev_intent_client_secret = $prev_intent_client_secret_obj->client_secret ?? '';
            $prev_intent_id = $prev_intent_client_secret_obj->intent_id ?? '';

            $stripe = new \Stripe\StripeClient([
                'api_key' => env('STRIPE_SECRET'),
                'stripe_version' => '2023-10-16',
            ]);

            $prev_payment_intent = $prev_intent_id ? $stripe->paymentIntents->retrieve($prev_intent_id) : null;

            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            $payment_intent = null;
            $intent_client_secret = '';

            $product = $order->product(); // TODO: try catch
            $checkout_id = $order->checkout_id;
            $pixels_ = Pixel::where('user_id', $product->user_id)->where('product_id', $product->id)->get();
            $pixels = [];
            foreach ($pixels_ as $pixel)
            {
                unset($pixel["access_token"]);
                $pixels[] = $pixel;
            }
            $customer = Customer::find($order?->customer_id);

            $usd_quote = (float) get_setting('usd_brl');
            $total = $order->total / $usd_quote;

            $locale = $product->language->code ?? '';

            $view = View::$view_method($this->indexFile, compact(
                'title',
                'context',
                'user',
                'url',
                'order',
                'pixels',
                'customer',
                'product',
                'order',
                'total',
                'intent_client_secret',
                'payment_intent',
                'locale'
            ));
        }

        catch (ModelNotFoundException)
        {
            $notfound = new NotFoundController($this->application);
            $view = $notfound->element(new Request);
        }

        return $view;
    }
}
