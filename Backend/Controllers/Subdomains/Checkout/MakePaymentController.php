<?php

namespace Backend\Controllers\Subdomains\Checkout;

use Backend\App;
use Backend\Exceptions\Checkout\CheckoutNotFoundException;
use Backend\Template\View;
use Backend\Enums\IPag\EIPagPaymentStatus;
use Backend\Enums\Order\EOrderStatus;
use Backend\Enums\Order\EOrderStatusDetail;
use Backend\Enums\Customer\ECustomerStatus;
use Backend\Enums\Purchase\EPurchaseStatus;
use Backend\Enums\PagarMe\EPagarMeChargeStatus;
use Backend\Enums\Lib\Session;
use Backend\Enums\Coupon\ECouponType;
use Backend\Enums\Product\EProductAffPaymentType;
use Backend\Enums\Product\EProductPaymentType;
use Backend\Enums\Product\EProductType;
use Backend\Enums\Subscription\ESubscriptionStatus;
// use Backend\Notifiers\Email\Mailer as Email;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Exceptions\Product\ProductNotFoundException;
use Backend\Exceptions\Address\IncompleteAddressException;
// use Backend\Controllers\Browser\NotFoundController;
use Backend\Entities\Abstracts\Iugu\IuguChargeQueue;
use Backend\Services\{IPag\IPag, NoxPay\NoxPayRest, PagarMe\PagarMe, OneSignal\OneSignal, GetNet\GetNet};
use Backend\Types\Response\ResponseStatus;
use Ezeksoft\PHPWriteLog\{Log, Table, Row};
use Ezeksoft\RocketZap\SDK as RocketZap;
use Ezeksoft\RocketZap\Http as RocketZapHttp;
use Ezeksoft\RocketZap\Enum\{ProjectType, Event as RocketZapEvent, PaymentMethod as RocketZapPaymentMethod};
use Backend\Enums\Product\EProductCookieMode;
use Ezeksoft\RocketZap\Exception\{
    CustomerRequiredException as RocketZapCustomerRequiredException,
    EventRequiredException as RocketZapEventRequiredException,
    ProductsRequiredException as RocketZapProductsRequiredException,
    OrderRequiredException as RocketZapOrderRequiredException
};
use chillerlan\QRCode\QRCode;
use Setono\MetaConversionsApi\Event\Event as FacebookEvent;
use Setono\MetaConversionsApi\Pixel\Pixel as FacebookPixel;
use Setono\MetaConversionsApi\Client\Client as FacebookClient;
use Backend\Entities\OrderEntity;
use Backend\Models\CardToken;
use Backend\Models\User;
use Backend\Models\Customer;
use Backend\Models\Product;
use Backend\Models\Checkout;
use Backend\Models\Order;
use Backend\Models\OrderMeta;
use Backend\Models\Purchase;
use Backend\Models\Orderbump;
use Backend\Models\Coupon;
use Backend\Models\ProductLink;
use Backend\Models\Pixel;
use Backend\Models\PagarmeCustomer;
use Backend\Models\IpagCustomer;
use Backend\Models\Plan;
use Backend\Models\Subscription;
use Backend\Models\Invoice;
use Backend\Models\IuguSeller;
use Backend\Services\Iugu\IuguRest;
use Backend\Types\Iugu\IuguChargeQueueDataList;
use Backend\Types\Response\EResponseDataStatus;
use Backend\Types\Response\ResponseData;

class MakePaymentController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->user = user();
        $this->subdomain = 'checkout';
        $this->domain = get_subdomain_serialized($this->subdomain);
    }

    public function main(Request $request)
    {
        // schema:
        // {protocol}://{subdomain}.{domain}/{sku}/{variation}

        $log = new Log;
        $response = [];

        try
        {
            $logged_user = $this->user;

            $body = $request->json();

            $email = strtolower($body->email ?? '');
            $product_id = $body->id;
            $checkout_id = $body->checkout_id ?: null;
            $installments = $body->installments ?? '';
            $variation = $body->variation ?? '';
            $sku = $body->sku;
            $gender = $body->gender ?? '';
            $customer_auth_token = $body->customer_auth_token ?? '';
            $card_index = intval($body->card_index ?? 0);
            $use_saved_card = boolval(intval($body->use_saved_card ?? 0));

            $product = Product::find($product_id);
            if (empty($product)) throw new ProductNotFoundException;

            $store_user_id = $product->user_id;

            // $checkout = Checkout::find($checkout_id);
            $plan = Plan::where('slug', $sku)->where('product_id', $product->id)->first();

            if (empty($plan))
                $checkout = Checkout::where('sku', $sku)->with('theme')->first();

            else
                $checkout = $variation
                    ? Checkout::where('sku', $variation)->with('theme')->first()
                    : Checkout::where('default', 1)->with('theme')->first();

            if (empty($checkout)) throw new CheckoutNotFoundException;

            $product_link = $sku && $variation ? ProductLink::where('slug', $variation)->where('product_id', $product->id)->first() : null;


            /**
             * Cookie
             */

            $aff_sku = '';

            // se ultimo clique
            if ($product->cookie_mode == EProductCookieMode::LAST_CLICK->value)
                $aff_sku = $_COOKIE['cookie_last_click'] ?? null;

            // cookie do primeiro click
            else if ($product->cookie_mode == EProductCookieMode::FIRST_CLICK->value)
                $aff_sku = $_COOKIE['cookie_first_click'] ?? null;

            $aff = User::where('sku', $aff_sku)->whereNotNull('sku')->first();


            /**
             * Calculos no total
             */

            $total = $plan?->price ?: $product_link?->amount ?: $product->price_promo ?: $product->price;
            $payment_method = $body->payment_method ?? '';

            if ($total >= 3)
            {
                if ($payment_method == 'pix')
                {
                    if (!empty($checkout))
                    {
                        if ($checkout->pix_discount_enabled) $total -= $checkout->pix_discount_amount;
                        else $total -= $product->pix_discount_amount;
                    }
                    else
                    {
                        if ($product->pix_discount_enabled) $total -= $product->pix_discount_amount;
                    }
                }

                if ($payment_method == 'billet')
                {
                    if (!empty($checkout))
                    {
                        if ($checkout->billet_discount_enabled) $total -= $checkout->billet_discount_amount;
                        else $total -= $product->billet_discount_amount;
                    }
                    else
                    {
                        if ($product->billet_discount_enabled) $total -= $product->billet_discount_amount;
                    }
                }

                if ($payment_method == 'credit_card')
                {
                    if (!empty($checkout))
                    {
                        if ($checkout->credit_card_discount_enabled) $total -= $checkout->credit_card_discount_amount;
                        else $total -= $product->credit_card_discount_amount;
                    }
                    else
                    {
                        if ($product->credit_card_discount_enabled) $total -= $product->credit_card_discount_amount;
                    }
                }
            }

            // orderbump
            $meta_orderbump = ["items" => []];

            foreach ($body->orderbumps as $orderbump_id)
            {
                $orderbump = Orderbump::where('id', $orderbump_id)->with('product')->first();
                if (!empty($orderbump))
                {
                    $orderbump_price = $orderbump->price_promo ?: $orderbump->price ?: $orderbump->product->price_promo ?: $orderbump->product->price;
                    $total += $orderbump_price;
                    $meta_orderbump["items"][] = [
                        "id" => $orderbump->id,
                        "total" => $orderbump_price,
                        "product_id" => $orderbump->product_id
                    ];
                }
            }
            $meta_orderbump["items"] = json_encode($meta_orderbump["items"]);

            $coupon_discount_amount = 0;
            $coupon_discount_type = '';
            $coupon_id = (float) get_session(Session::CHECKOUT, 'coupon_applied_id');
            if (!empty($coupon_id))
            {
                $coupon = Coupon::where('id', $coupon_id)->first();
                if (!empty($coupon))
                {
                    if ($coupon->type == ECouponType::PERCENT->value)
                    {
                        $d = ($total * ($coupon->discount / 100));
                        $total = $total - $d;
                        $coupon_discount_amount = $d;
                        $coupon_discount_type = $coupon->type;
                    }

                    else if ($coupon->type == ECouponType::PRICE->value)
                    {
                        $d = $coupon->discount;
                        $total = $total - $d;
                        $coupon_discount_amount = $d;
                        $coupon_discount_type = $coupon->type;
                    }

                    $d = null;
                }
            }

            // preco do frete
            if (EProductType::PHYSICAL->value == $product->type)
                $total += $product->shipping_cost;

            // parcelas
            $total_without_cc_fee = $total;

            if ($installments ==  2) $total += $total * insx(2);
            if ($installments ==  3) $total += $total * insx(3);
            if ($installments ==  4) $total += $total * insx(4);
            if ($installments ==  5) $total += $total * insx(5);
            if ($installments ==  6) $total += $total * insx(6);
            if ($installments ==  7) $total += $total * insx(7);
            if ($installments ==  8) $total += $total * insx(8);
            if ($installments ==  9) $total += $total * insx(9);
            if ($installments == 10) $total += $total * insx(10);
            if ($installments == 11) $total += $total * insx(11);
            if ($installments == 12) $total += $total * insx(12);

            // --

            // validar endereco
            if ($payment_method == 'credit_card' || $payment_method == 'billet')
            {
                if (empty($body?->city)) throw new IncompleteAddressException("Informe sua cidade.");
                if (empty($body?->state)) throw new IncompleteAddressException("Informe seu Estado.");
                if (empty($body?->zipcode)) throw new IncompleteAddressException("Informe seu CEP.");
            }

            $ipag = new IPag;
            $pagarme = new PagarMe;
            $getnet = new GetNet;

            $data_customer =
                [
                    "name" => $body->name,
                    "email" => $email,
                    "cpf_cnpj" => $body->cpf_cnpj
                ];
            $data_customer_extra =
                [
                    "phone" => preg_replace("/\D/", "", $body->phone)
                ];
            $data_address =
                [
                    "billing_address" =>
                    [
                        "street" => $body->street,
                        "number" => $body->number,
                        "district" => $body->neighborhood,
                        "complement" => $body->complement,
                        "city" => $body->city,
                        "state" => $body->state,
                        "zipcode" => $body->zipcode,
                    ]
                ];
            $data_info =
                [
                    "total" => $total,
                    "payment_method" => $payment_method,
                    "flag" => $body->flag,
                ];
            $data_cc =
                [
                    "holdername" => $body->holdername,
                    "card_number" => $body->card_number,
                    "month" => $body->month,
                    "year" => $body->year,
                    "cvv" => $body->cvv,
                    "customer" => $data_customer + $data_customer_extra + $data_address,
                    "product" => json_decode(json_encode($product))
                ];
            $data = $data_info + $data_cc;
            $data["transaction_id"] = strtoupper(uniqid()) . $store_user_id;
            $data["use_saved_card"] = $use_saved_card;

            // if ($payment_method == 'credit_card') 
            $data["installments"] = $body->installments ?? 1;

            $upsell_token = null;
            $card = null;

            $customer = Customer::where('email', $email)->first();
            $pg_customer = $customer?->id ? PagarmeCustomer::where('customer_id', $customer->id)->where('pm_customer_id', '<>', null)->orderBy('id', 'DESC')->first() : null;
            $ipag_customer = $customer?->id ? IpagCustomer::where('customer_id', $customer->id)->where('card_token', '<>', null)->orderBy('id', 'DESC')->first() : null;

            if ($payment_method == 'credit_card' && $product->payment_type === EProductPaymentType::RECURRING->value)
            {
                $data["customer_id"] = $pg_customer?->pm_customer_id;

                $data["plan"] = [];
                $data["plan"]["interval"] = $product->recurrence_interval;
                $data["plan"]["interval_count"] = $product->recurrence_interval_count;
            }

            if (!empty($customer))
            {
                // garante o token upsell seja do email informado na requisicao (seguranca contra fraudes) 
                if (strlen($customer_auth_token) > 10 && $customer_auth_token == $customer->upsell_token)
                    $upsell_token = $customer->upsell_token;

                // se o customer esta autenticado
                if ($upsell_token)
                {
                    if (!empty($pg_customer))
                    {
                        $get_cards = $pagarme->getCards($pg_customer->pm_customer_id);
                        $get_cards = json_decode($get_cards ?? '{}');
                        $cards = $get_cards->data ?? [];
                    }

                    if (!empty($ipag_customer))
                    {
                        $card_tokens = IpagCustomer::where('customer_id', $customer->id)->where('card_token', '<>', null)->orderBy('id', 'DESC')->get();
                        $cards = [];
                        foreach (array_map(fn ($card) => json_decode($card), $ipag->getCards(array_map(fn ($row) => $row->card_token, [...$card_tokens]))) as $card_)
                        {
                            $cards[] = (object) [
                                "id" => $card_->token,
                                "first_six_digits" => $card_->attributes->card->bin,
                                "last_four_digits" => $card_->attributes->card->last4,
                                "brand" => ucwords($card_->attributes->card->brand),
                                "holder_name" => $card_->attributes->card->holder,
                                "holder_document" => $card_->attributes->holder->cpf,
                                "exp_month" => '0',
                                "exp_year" => '0000',
                                "status" => $card_->attributes->card->is_active ? 'active' : '',
                                "type" => "credit",
                                "created_at" => date("Y-m-d\TH:i:s\Z", strtotime($card_->attributes->created_at)),
                                "updated_at" => date("Y-m-d\TH:i:s\Z", strtotime($card_->attributes->updated_at))
                            ];
                        }
                    }
                }
            }

            if (!empty($cards)) $card = $cards[$card_index] ?? $cards[0] ?? null;

            if (!empty($card) && $use_saved_card)
            {
                $data["card_id"] = $card->id;
            }

            $data["card_index"] = $card_index;

            // $gateway = 'pagarme';
            // $gateway = 'ipag';
            $gateway = get_setting('gateway') ?: 'pagarme';
            $charge_status = '';

            // --

            $order = new Order;
            $order->save();
            $order->uuid = uuid();
            $order->user_id = $store_user_id;
            $order->total = $total;
            $order->total_seller = 0;
            $order->total_vendor = 0;
            $order->checkout_id = $checkout_id;


            // $total_without_cc_fee = valor total
            $fee_percent = doubleval(get_setting('transaction_fee')) / 100; // porcentagem que o vendor cobra por transacao
            $fee_rs = doubleval(get_setting('transaction_fee_extra')); // taxa extra em reais que o vendor cobra por transacao
            $vendor_fee = $total_without_cc_fee * $fee_percent; // quanto o vendor vai receber em reais
            $total_f = $total_without_cc_fee;

            $fee_rs_apply = $total >= 3 ? $fee_rs : 0; // se o total deu um valor minimo para aplicar as taxas, usar as taxas, caso contrario, ficar 0 (taxa)

            /**
             * vendor: RocketLeads
             * seller: Dono do checkout
             * aff: Afiliado
             */

            // switch ($payment_method)
            // {
            //     case 'credit_card':
            //         // if ($installments <= 6) // ate 6x
            //         //     $percent = .0699;
            //         // else // mais de 6x
            //         //     $percent = .0999;

            //         $order->total_seller = $total_f - $fee_rs - $vendor_fee;
            //         $order->total_seller = $order->total_seller > 0 ? $order->total_seller : 0;
            //         $order->total_aff = $order->total_seller - $aff_amount;
            //         $order->total_seller = $order->total_seller - $order->total_aff;
            //         $order->total_vendor = $vendor_fee > 0 ? $vendor_fee : 0;
            //         break;

            //     case 'pix':
            //         $order->total_seller = $total_f - $fee_rs - $vendor_fee; // 149 - 2 - 11.9051 = 135.0949
            //         $order->total_seller = $order->total_seller > 0 ? $order->total_seller : 0;
            //         $aff_amount = $product->affiliate_payment_type == EProductAffPaymentType::PERCENT->value 
            //         ? $order->total_seller * ($product->affiliate_amount / 100) : $product->affiliate_amount;
            //         $order->total_aff = $aff_amount;
            //         $order->total_seller = $order->total_seller - $order->total_aff;
            //         $order->total_vendor = $vendor_fee > 0 ? $vendor_fee : 0;
            //         break;

            //     case 'billet':
            //         $order->total_seller = $total_f - $fee_rs - $vendor_fee;
            //         $order->total_seller = $order->total_seller > 0 ? $order->total_seller : 0;
            //         $order->total_aff = $order->total_seller - $aff_amount;
            //         $order->total_seller = $order->total_seller - $order->total_aff;
            //         $order->total_vendor = $vendor_fee > 0 ? $vendor_fee : 0;
            //         break;
            // }

            $order->total_seller = $total_f - $fee_rs - $vendor_fee; // 149 - 2 - 11.9051 = 135.0949
            $order->total_seller = $order->total_seller > 0 ? $order->total_seller : 0;

            $aff_amount = $product->affiliate_payment_type == EProductAffPaymentType::PERCENT->value
                ? $order->total_seller * ($product->affiliate_amount / 100) : $product->affiliate_amount;

            if (!empty($aff))
            {
                $order->total_aff = $aff_amount;
                $order->total_seller = $order->total_seller - $order->total_aff;
            }

            $order->total_vendor = $vendor_fee > 0 ? $vendor_fee : 0;

            $data["total_seller"] = $order->total_seller;
            $data["total_vendor"] = $order->total_vendor;
            $data["total_aff"] = $order->total_aff;
            $data["store_user_id"] = $store_user_id;

            // echo "$total_f * ($product->affiliate_amount / 100): ".($total_f * ($product->affiliate_amount / 100)) . "\n";
            // echo "product->affiliate_amount: ".$product->affiliate_amount . "\n";
            // echo "aff_amount: ".$aff_amount . "\n";
            // echo "total_f: ".$total_f . "\n";
            // echo "fee_rs: ".$fee_rs . "\n";
            // echo "vendor_fee: ".$vendor_fee . "\n";
            // echo "total_seller: ".$data["total_seller"] . "\n";
            // echo "total_vendor: ".$data["total_vendor"] . "\n";
            // echo "total_aff: ".$data["total_aff"] . "\n";

            // die();

            // --
            $transaction_id = "";
            $pagarme_subscription_id = null;

            if (!$product->is_free)
            {

                if ($gateway == 'pagarme')
                {
                    if ($payment_method == 'credit_card')
                    {
                        $gateway = 'getnet';
                    }
                }

                if ($gateway == 'getnet')
                {
                    $getnet = new GetNet;
                    $access_token = $getnet->accessToken();
                    $gn_customer_id = 'cus_' . $user->id;
                    $object = (array) json_decode($getnet->tokenizeCard($access_token, [
                        'card_number' => $data_cc['card_number'],
                        'customer_id' => $gn_customer_id
                    ])); // $number_token
                    extract($object);
                    print_r($number_token);

                    // if ($failed) $gateway = 'pagarme';
                }

                if ($gateway == 'pagarme')
                {
                    $payment = json_decode(match ($payment_method)
                    {
                        'credit_card' => $product->payment_type === EProductPaymentType::RECURRING->value
                            ? $pagarme->subscription($data) : $pagarme->creditCard($data),
                        'pix' => $pagarme->pix($data),
                        'billet' => $pagarme->billet($data),
                    });

                    (new Log)->write(base_path('logs/pagarme_request.log'), json_encode($data));
                    (new Log)->write(base_path('logs/pagarme_response.log'), json_encode($payment));

                    $charges = $payment?->charges ?? [];
                    $charge = !empty($charges) ? $charges[sizeof($charges) - 1] : null;
                    $customer_id = $payment?->customer?->id ?? '';

                    $charge_status = $charge?->status ?? '';
                    $transaction_id = $charge_id = $charge?->id ?? '';

                    if ($payment_method == 'credit_card')
                    {
                        if ($product->payment_type === EProductPaymentType::RECURRING->value)
                        {
                            $last_charge = $customer_id ? json_decode($pagarme->getLastCharge(compact('customer_id')) ?? '{}') : null;
                            $charge = $last_charge?->data[0] ?? null;
                            $pagarme_subscription_id = $payment?->id ?? '';
                            add_ordermeta($order->id, 'payment_pagarme_subscription_id', $pagarme_subscription_id);
                            $transaction_id = $pagarme_subscription_id;
                            add_ordermeta($order->id, 'payment_pagarme_charge_id', $charge_id);
                        }
                    }

                    // caso dee falha no pagamento com pagar.me, tentar com ipag
                    // if ($payment_method == 'credit_card' && $charge?->status == EPagarMeChargeStatus::FAILED)
                    //     $gateway = 'ipag';
                }

                if ($gateway == 'ipag')
                {
                    $payment = json_decode(match ($payment_method)
                    {
                        'credit_card' => $product->payment_type === EProductPaymentType::RECURRING->value
                            ? $ipag->subscription($data) : $ipag->creditCard($data),
                        'pix' => $ipag->pix($data),
                        'billet' => $ipag->billet($data),
                    });

                    (new Log)->write(base_path('logs/ipag_request.log'), json_encode($data));
                    (new Log)->write(base_path('logs/ipag_response.log'), json_encode($payment));

                    $transaction_id = $data["transaction_id"];
                    $ipag_payment_id = $payment->id ?? '';
                    add_ordermeta($order->id, 'payment_ipag_payment_id', $ipag_payment_id);
                    $ipag_subscription_id = $payment->attributes->subscription->id ?? '';
                    add_ordermeta($order->id, 'payment_ipag_subscription_id', $ipag_subscription_id);
                    if ($ipag_subscription_id) $transaction_id = "ipag_$ipag_subscription_id";
                }
            }

            else
            {
                $payment = (object) ['is_free' => true];
            }

            // $charge = '';
            // if ($gateway == 'pagarme' && !empty($payment?->charges))
            // {
            //     $charge = $payment?->charges[sizeof($payment?->charges)-1] ?? null;
            // }

            $order->transaction_id = $transaction_id;

            /**
             * Paid
             */
            $paid = false;

            if ($gateway == "ipag")
            {
                $code = $payment?->attributes?->status?->code ?? '';
                $paid = $code == EIPagPaymentStatus::PRE_AUTHORIZED
                    || $code == EIPagPaymentStatus::CAPTURED;
            }

            if ($gateway == "pagarme")
                $paid = ($payment?->status ?? '') == EPagarMeChargeStatus::PAID;

            if ($product->is_free)
            {
                $paid = 1;
                $order->transaction_id = uniqid();
            }

            // if ($paid)
            // {
            //     $order->status = EOrderStatus::APPROVED;
            //     $order->status_details = EOrderStatusDetail::APPROVED;
            // }

            if (!$paid)
            {
                $order->status = EOrderStatus::PENDING;
                $order->status_details = EOrderStatusDetail::PENDING;
            }

            // END

            $order->aff_id = $aff?->id;
            $order->save();
            // $order = Order::find($order->id);

            $cc = $data_cc['card_number'];
            $first_six_digits = substr($cc, 0, 6);
            $last_four_digits = substr($cc, -4);

            if ($payment_method == 'credit_card')
            {
                add_ordermeta($order->id, 'payment_installments', $data["installments"]);
                add_ordermeta($order->id, 'payment_cc_last_four_digits', $last_four_digits);
                add_ordermeta($order->id, 'payment_cc_first_six_digits', $first_six_digits);
                add_ordermeta($order->id, 'payment_cc_flag', $data_info['flag']);
            }

            $billet_code = '';
            $billet_pdf = '';
            $pix_code = '';
            $pix_image = '';

            if ($payment_method == 'billet')
            {
                $ordermeta = new OrderMeta;
                $ordermeta->order_id = $order->id;
                $ordermeta->name = 'payment_billet_code';

                if ($gateway == 'ipag')
                    $ordermeta->value = $payment->attributes->boleto->digitable_line ?? '';
                else if ($gateway == 'pagarme')
                    $ordermeta->value = $charge->last_transaction->line ?? '';
                // $charge->last_transaction->barcode ?? ''; // link da imagem do codigo de barras

                $ordermeta->save();
                $billet_code = $ordermeta->value;

                $ordermeta = new OrderMeta;
                $ordermeta->order_id = $order->id;
                $ordermeta->name = 'payment_billet_link';

                if ($gateway == 'ipag')
                    $ordermeta->value = $payment->attributes->boleto->link ?? '';
                else if ($gateway == 'pagarme')
                    $ordermeta->value = $charge->last_transaction->pdf ?? '';

                $ordermeta->save();
                $billet_pdf = $ordermeta->value;
            }

            if ($payment_method == 'pix')
            {
                $ordermeta = new OrderMeta;
                $ordermeta->order_id = $order->id;
                $ordermeta->name = 'payment_pix_code';
                if ($gateway == 'ipag')
                    $ordermeta->value = $payment->attributes->pix->qrcode ?? '';
                else if ($gateway == 'pagarme')
                    $ordermeta->value = $charge->last_transaction->qr_code ?? '';
                $ordermeta->save();
                $pix_code = $ordermeta->value;

                $ordermeta = new OrderMeta;
                $ordermeta->order_id = $order->id;
                $ordermeta->name = 'payment_pix_image';
                if ($gateway == 'ipag')
                    $ordermeta->value = $payment->attributes->pix->qrcode ?? '';
                else if ($gateway == 'pagarme')
                    $ordermeta->value = $charge->last_transaction->qr_code ?? '';
                if ($ordermeta->value) $ordermeta->value = (new QRCode)->render($ordermeta->value);
                $ordermeta->save();
                $pix_image = $ordermeta->value;
            }

            add_ordermeta($order->id, 'product_id', $product->id);

            $meta_address = $data["customer"]["billing_address"];
            foreach ($meta_address as $key => $value)
                add_ordermeta($order->id, "address_$key", $value);

            $meta_customer = $data_customer + $data_customer_extra;
            foreach ($meta_customer as $key => $value)
                add_ordermeta($order->id, "customer_$key", $value);

            $meta_info = $data_info;
            foreach ($meta_info as $key => $value)
                add_ordermeta($order->id, "info_$key", $value);

            add_ordermeta($order->id, "product_price", $product->price_promo ?: $product->price);
            add_ordermeta($order->id, "product_link", $product_link->id ?? '');

            if ($product->price_promo)
                add_ordermeta($order->id, "product_price_promo_diff", doubleval($product->price_promo) - doubleval($product->price));

            foreach ($meta_orderbump as $key => $value)
                add_ordermeta($order->id, "orderbump_$key", $value);

            if (!empty($coupon))
            {
                add_ordermeta($order->id, "coupon", json_encode([
                    "id" => $coupon->id,
                    "amount" => $coupon_discount_amount,
                    "type" => $coupon_discount_type
                ]));
            }

            $payment->order_uuid = $order->uuid;
            $payment->gateway = $gateway;

            if (empty($customer))
            {
                $upsell_token = ghash();
                $password = time();
                $customer = new Customer;
                $customer->name = $body->name;
                $customer->email = $email;
                $customer->user_id = $product->user_id;
                $customer->password = hash_make($password);
                $customer->access_token = ghash();
                $customer->status = ECustomerStatus::ACTIVE->value;
                $customer->doc = $body->cpf_cnpj;
                $customer->phone = preg_replace("/\D/", "", "55" . $body->phone);
                $customer->address_street = $body->street;
                $customer->address_number = $body->number;
                $customer->address_district = $body->neighborhood;
                $customer->address_complement = $body->complement;
                $customer->address_city = $body->city;
                $customer->address_state = $body->state;
                $customer->address_zipcode = $body->zipcode;
                $customer->address_country = 'BR';
                $customer->gender = $gender ?: null;
                $customer->upsell_token = $upsell_token;
                $customer->upsell_token_at = date("Y-m-d H:i:s", strtotime(today() . " + 1 hour"));
                $customer->save();

                $email_vars[] = ["app" => null];
                $email_vars["app"] = (object) compact('product', 'order', 'payment', 'customer');
                $email_vars["app"]->password = $password;

                /**
                 * Envia e-mail para o cliente com os dados de login
                 */
                // Email::to($email_vars["app"]->customer->email)
                //     ->title('Bem-vindo a Rocketpays')
                //     ->subject("Seu acesso a área restrita")
                //     ->body(Email::view('newCustomer', $email_vars))
                //     ->send();
            }

            $customer->user_agent = $_SERVER['HTTP_USER_AGENT'];
            $customer->ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'];
            $customer->save();

            $order->customer_id = $customer->id;
            $order->save();
            $order_entity = new OrderEntity($order);
            $order_products = $order_entity->getProducts();
            $order_user = $order_entity->getUser();

            $pagarme_customer = new PagarmeCustomer;
            $pagarme_customer->pm_customer_id = $payment->customer->id ?? null;
            if ($pagarme_customer->pm_customer_id)
            {
                $pagarme_customer->customer_id = $customer->id;
                $pagarme_customer->save();
            }

            $ipag_customer = new IpagCustomer;
            $ipag_customer->card_token = $payment->attributes->card->token ?? null;
            if ($ipag_customer->card_token)
            {
                $ipag_customer->customer_id = $customer->id;
                $ipag_customer->save();
            }

            if ($product->payment_type === EProductPaymentType::RECURRING->value)
            {
                $subscription = new Subscription;
                $subscription->status = ESubscriptionStatus::PENDING;
                $subscription->customer_id = $customer->id;
                $subscription->order_id = $order->id;
                $subscription->interval = $product->recurrence_interval;
                $subscription->interval_count = $product->recurrence_interval_count;
                $subscription->save();

                $invoice = new Invoice;
                $invoice->order_id = $order->id;
                $invoice->save();
            }

            $_SESSION['customer_access_token'] = $customer->access_token;

            $payment->app = (object) [
                'customer_id' => $customer->id,
                'customer_email' => $customer->email,
                'customer_upsell_token' => $upsell_token,
                'total' => $total,
                'order_id' => $order->id
            ];

            // se foi aprovado no cartao de credito
            // if ($paid)
            // {
            //     // $meta_products = OrderMeta::where('order_id', $order->id)->where('name', 'product_id')->get();

            //     foreach ($order_products->base_products as $meta_product)
            //     {
            //         $purchase = new Purchase;
            //         $purchase->customer_id = $customer->id;
            //         $purchase->product_id = $meta_product->value;
            //         $purchase->order_id = $order->id;
            //         $purchase->status = EPurchaseStatus::ACTIVE;
            //         $purchase->save();
            //     }
            // }

            $extra_info = [
                "payment_pix_code" => $pix_code,
                "payment_pix_image" => $pix_image,
                "payment_billet_code" => $billet_code,
                "payment_billet_link" => $billet_pdf,
                "payment_cc_first_six_digits" => $first_six_digits,
                "payment_cc_last_four_digits" => $last_four_digits,
                "payment_cc_flag" => $data_info["flag"],
                "payment_installments" => $data["installments"],
            ];

            // dispara evento de pixel do facebook
            $this->dispatchPixelFacebook($order, $checkout, $customer, $order_products);

            // criar uma automacao na rocketzap que envia mensagem de whatsapp para o cliente
            // $this->dispatchRocketZap($order, $customer, $payment_method, $order_products, $order_user, $paid, $gateway, $charge_status, $extra_info);

            $onesignal = new OneSignal;
            $onesignal->setTitle(match ($payment_method)
            {
                "pix" => "Pix gerado!",
                "billet" => "Boleto impresso!",
                "credit_card" => "Venda em análise!"
            }/* ." ".join(", ", $order_products->product_names)."." */);
            $onesignal->setDescription("Sua comissão: R$ " . currency($order->total_seller));
            $onesignal->addExternalUserID($order_user->email);
            $onesignal_result = $onesignal->pushNotification();
            (new Log)->write(base_path("logs/onesignal.log"), "\n===========================\n" .
                "REQUEST: " . json_encode($onesignal->getPayload()) . "\nRESPONSE: " . $onesignal_result);

            $response = [
                "status" => "success",
                "message" => "Pedido realizado com sucesso.",
                "data" => $payment
            ];
        }

        catch (ProductNotFoundException $ex)
        {
            $response = ["status" => "error", "message" => "Produto não encontrado."];
        }

        catch (CheckoutNotFoundException $ex)
        {
            $response = ["status" => "error", "message" => "Checkout não encontrado."];
        }

        catch (IncompleteAddressException $ex)
        {
            $response = ["status" => "error", "message" => "Endereço incompleto. " . $ex->getMessage()];
        }

        finally
        {
            Response::json($response);
        }
    }

    private function dispatchRocketZap(
        Order $order,
        Customer $customer,
        $payment_method,
        $order_products,
        $order_user,
        $paid,
        $gateway,
        $charge_status,
        $extra_info
    )
    {
        if (empty($order_user->user_id_rocket_panel)) return;

        $aux = explode(" ", $customer->name);
        $customer_first_name = $aux[0];
        $customer_last_name = substr($customer->name, strlen($customer_first_name) + 1, strlen($customer->name));

        $rocketzap = RocketZap::SDK($order_user->user_id_rocket_panel);

        // adaptar para executar no localhost (Ezequiel)
        if (str_contains(env('URL'), env('EZEQUIEL_LOCAL_IF_URL_CONTAINS_THIS'))) $rocketzap->setEndpoint(env('EZEQUIEL_LOCAL_ROCKETZAP_ENDPOINT'));

        $customer = $rocketzap->customer()
            ->setId($customer->id)
            ->setFirstName($customer_first_name)
            ->setLastName($customer_last_name)
            ->setEmail($customer->email)
            ->setPhone($customer->phone);

        foreach ($order_products->all_products as $product)
        {
            $rocketzap->addProduct(
                $rocketzap->product()
                    ->setId($product->id)
                    ->setName($product->name)
                    ->setPrice($product->price)
            );
        }

        $merchant = $rocketzap->merchant()
            ->setId($order_user->id)
            ->setName($order_user->name)
            ->setEmail($order_user->email);

        if ($payment_method == "pix") $rocketzap
            ->setEvent(RocketZapEvent::PIX_GENERATED)
            ->setPix(
                $pix = $rocketzap->pix()
                    ->setText($pix_text = $extra_info["payment_pix_code"])
                    ->setImage($pix_image = $extra_info["payment_pix_image"])
            )
            ->setPaymentMethod(RocketZapPaymentMethod::PIX);

        if ($payment_method == "billet") $rocketzap
            ->setEvent(RocketZapEvent::BILLET_PRINTED)
            ->setBillet(
                $billet = $rocketzap->billet()
                    ->setText($billet_text = $extra_info["payment_billet_code"])
                    ->setPdf($billet_pdf = $extra_info["payment_billet_link"])
            )
            ->setPaymentMethod(RocketZapPaymentMethod::BILLET);

        if ($payment_method == "credit_card" && $charge_status == EPagarMeChargeStatus::FAILED)
            $rocketzap
                ->setEvent(RocketZapEvent::REJECTED)
                ->setCreditCard(
                    $credit_card = $rocketzap->creditCard()
                        ->setFirstSixDigits($extra_info["payment_cc_first_six_digits"])
                        ->setLastFourDigits($extra_info["payment_cc_last_four_digits"])
                        ->setFlag($extra_info["payment_cc_flag"])
                        ->setInstallments($extra_info["payment_installments"])

                )
                ->setPaymentMethod(RocketZapPaymentMethod::CREDIT_CARD);

        try
        {
            if (in_array($payment_method, ['credit_card', 'billet', 'pix']))
            {
                $rocketzap
                    ->setOrder($rocketzap->order()->setId($order->uuid)->setTotal($order->total))
                    ->setCustomer($customer)
                    ->setMerchant($merchant)
                    ->save([ProjectType::AUTOMATION]);

                (new Log)->write(base_path('logs/rocketzap.log'), ["now" => today(), "request" => $rocketzap->getJson()]);
                list($automation) = $rocketzap->getResponses();
                $automation->http->finally(fn (RocketZapHttp $response) => (new Log)->write(base_path('logs/rocketzap.log'), $response->getJson()));
            }
        }

        catch (RocketZapCustomerRequiredException $ex)
        {
            return $ex->getMessage();
        }

        catch (RocketZapEventRequiredException $ex)
        {
            return $ex->getMessage();
        }

        catch (RocketZapProductsRequiredException $ex)
        {
            return $ex->getMessage();
        }

        catch (RocketZapOrderRequiredException $ex)
        {
            return $ex->getMessage();
        }
    }

    private function dispatchPixelFacebook(Order $order, Checkout $checkout, Customer $customer, $order_products)
    {
        $log = new Log;
        $now = today();

        // busca todos os dados dos produtos comprados no checkout (apenas os produtos base, ou seja, que nao sao adicionais como um orderbump)
        $products = $order_products->all_products;
        $products_base = $order_products->base_products;
        $product_names = $order_products->product_names;

        // $meta_products = $order_products->base_products;
        // foreach ($meta_products as $meta_product)
        // {
        //     $product = Product::find($meta_product->value);
        //     if (!empty($product))
        //     {
        //         $products_base[] = $product;
        //         $product_names[] = $product->name;
        //         $products[] = $product;
        //     }
        // }

        // lista os produtos em orderbump e adiciona na lista completa de produtos comprados
        // $meta_orderbumps = OrderMeta::where('order_id', $order->id)->where('name', 'orderbump_items')->first();
        // $meta_orderbumps = json_decode($meta_orderbumps->value ?? '[]');
        // foreach ($meta_orderbumps as $orderbump)
        // {
        //     $product = Product::find($orderbump->product_id);
        //     if (!empty($product)) 
        //     {
        //         $product_names[] = $product->name;
        //         $products[] = $product;
        //     }
        // }

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
        $pixels_ = array_filter($aux, function ($item)
        {
            unset($item->access_token);
            return $item;
        });

        $log->write(base_path('logs/pixel.log'), "\n============================\n[$now] " . FacebookEvent::EVENT_INITIATE_CHECKOUT . "\n" . json_encode($pixels_));

        // itera pixels
        foreach ($facebook_pixels as $pixel)
        {
            // pixel do facebook
            if ($pixel->platform == "facebook" && $pixel->access_token)
            {
                $echo_pixel = "pixel: $pixel->content\n";

                $event = new FacebookEvent(FacebookEvent::EVENT_INITIATE_CHECKOUT);
                $event->pixels[] = new FacebookPixel($pixel->content, $pixel->access_token);

                if (!empty($event->pixels))
                {
                    $user_url = null;
                    if ($user_domain = $pixel->domain->full_domain) $user_url = "https://$user_domain";
                    $event->eventSourceUrl = ($user_url ?: get_subdomain_serialized('checkout')) . "/" . $checkout->sku;
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

                    $client = new FacebookClient();
                    $client->sendEvent($event);

                    $log->write(base_path('logs/pixel.log'), "$echo_pixel | " . json_encode([
                        "url" => $event->eventSourceUrl,
                        "custom" => $event->customData,
                        "user" => $event->userData,
                    ]));
                }
            }
        }

        $log->write(base_path('logs/pixel.log'), "----------------------------\n\n");
    }

    public function pay(Request $request)
    {
        try {
            $descriptor = env('IUGU_DESCRIPTOR');
            $test = env('IUGU_TEST') == 'true' ? 'true' : 'false';
            $body = $request->json();
            $store_user_id = sanitize($body->store_user_id ?? '');
            $product_id = sanitize($body->product_id ?? '');
            $checkout_id = sanitize($body->checkout_id ?? '');
            $name = sanitize($body->name ?? '');
            $email = sanitize($body->email ?? '');
            // $months = sanitize($body->months ?? 1)
            $cpf_cnpj = sanitize($body->cpf_cnpj ?? '');
            $cc = preg_replace('/\D/', '', sanitize($body->cc ?? ''));
            $cvc = sanitize($body->cvc ?? '');
            $name = sanitize($body->name ?? '');
            $expiration = sanitize($body->expiration ?? '');
            $testmode_key = sanitize($body->_b ?? '');
            $gateway = sanitize($body->gateway ?? '');

            $aux = explode("/", $expiration);
            $month = $aux[0] ?? '';
            $year = $aux[1] ?? '';

            $first_name = explode(" ", $name)[0];
            $last_name = substr($name, strlen($first_name) + 1, strlen($name));

            $card_tokenized = aes_encode_db("$cc $month/$year $cvc");

            $product = Product::where('id', $product_id)->first();

            if (empty($product)) return Response::json(new ResponseData([
                'status' => EResponseDataStatus::ERROR,
                'message' => 'Este produto não foi encontrado.'
            ]), new ResponseStatus('400 Bad Request'));

            $iugu_seller = IuguSeller::where('user_id', $product->user_id)->orderBy('id', 'DESC')->first();
            $account_id = ($iugu_seller->account_id ?? '') ?: env('IUGU_ACCOUNT_ID');
            $iugu_token = ($iugu_seller->live_api_token ?? '') ?: env('IUGU_API_TOKEN');
            if ($test == 'true') $account_id = env('IUGU_ACCOUNT_ID');
            $payload_token = [
                'account_id' => env('IUGU_ACCOUNT_ID'),
                'method' => 'credit_card',
                'test' => $test,
                'data' => [
                    'number' => $cc,
                    'verification_value' => $cvc,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'month' => $month,
                    'year' => $year
                ]
            ];

            $headers = ['Content-Type' => 'application/json'];

            $response = IuguRest::request(
                verb: 'POST',
                url: '/payment_token',
                headers: $headers,
                body: json_encode($payload_token),
                timeout: 10
            );

            if ($response->status_code !== 200) return Response::json(new ResponseData([
                'status' => EResponseDataStatus::ERROR,
                'message' => 'Falha ao utilizar este cartão.',
                'data' => $response->json->errors ?? []
            ]), new ResponseStatus('400 Bad Request'));

            $token = $response->json->id ?? '';

            $customer = Customer::where('email', $email)->first();
            if (empty($customer)) {
                $customer = new Customer;
                $customer->name = $name;
                $customer->email = $email;
                $customer->user_id = $store_user_id;
                $customer->password = null;
                $customer->access_token = ghash();
                $customer->one_time_access_token = $customer->access_token;
                $customer->status = ECustomerStatus::ACTIVE->value;
                $customer->address_country = 'BR';
                $customer->user_agent = $_SERVER['HTTP_USER_AGENT'];
                $customer->ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'];
                $customer->save();
            }

            // $order = new Order;
            $current_order_id = $_SESSION['current_order'];
            $order = Order::find($current_order_id);

            if (empty($order)) return Response::json(new ResponseData([
                'status' => EResponseDataStatus::ERROR,
                'message' => 'Este pedido não foi encontrado.'
            ]), new ResponseStatus('400 Bad Request'));


            $currency_quote = $product->currency == 'brl' ? 1 : (float)get_setting(($product->currency ?: 'usd') . '_brl');
            if ($currency_quote <= 0) return Response::json(new ResponseData([
                'status' => EResponseDataStatus::ERROR,
                'message' => 'A cotação da moeda não pode ser menor que zero.'
            ]), new ResponseStatus('400 Bad Request'));

            $product_price_int = $total_int = intval(($product->price_promo ?: $product->price ?: 0) * 100); // total em inteiro
            $product_price = $product_price_int / 100; // total em decimal

            if ($total_int <= 0) return Response::json(new ResponseData([
                'status' => EResponseDataStatus::ERROR,
                'message' => 'O valor não pode ser menor que zero.'
            ]), new ResponseStatus('400 Bad Request'));

            $total_int = intval($total_int * $currency_quote);

            // total em decimal (moeda: BRL)
            $total = intval($total_int) / 100;

            $gateway_fee_percent = doubleval(get_setting('gateway_fee_percent')) / 100;
            $gateway_fee_price = doubleval(get_setting('gateway_fee_price'));

            $transaction_fee_extra = doubleval(get_setting('transaction_fee_extra'));
            $platform_fee = doubleval(get_setting('transaction_fee')) / 100;

            $total_gateway = $total * $gateway_fee_percent + $gateway_fee_price;
            $total_seller = $total - $total * $platform_fee - $transaction_fee_extra;
            $total_vendor = $total - $total_seller - $total_gateway;

            $order->user_id = $store_user_id;
            $order->total = $total;
            $order->total_seller = $total_seller;
            $order->total_vendor = $total_vendor;
            $order->total_gateway = $total_gateway;
            $order->checkout_id = $checkout_id;
            $order->customer_id = $customer->id;
            $order->currency_symbol = $product->currency;
            $order->currency_total = $product_price;
            $order->status = EOrderStatus::PENDING;
            $order->status_details = EOrderStatusDetail::PENDING;
            $order->gateway = $gateway;

            $id_date = date('Ymd');
            $id_card = substr($cc, 0, 6) . substr($cc, -4);
            $id_price = preg_replace('/\D/', '', $order->total);

            $order->alert_id = "$id_date-$id_card";
            $order->save();

            add_ordermeta($order->id, "customer_name", $customer->name);
            add_ordermeta($order->id, "customer_email", $customer->email);

            add_ordermeta($order->id, 'product_id', $product->id);
            add_ordermeta($order->id, 'product_price', $product->price_promo ?: $product->price);
            add_ordermeta($order->id, 'info_total', $product->price_promo ?: $product->price);

            $payload_charge = [
                'token' => $token,
                // "discount_cents" => 300,
                'keep_dunning' => true,
                'items' => [
                    'description' => $product->name,
                    'quantity' => 1,
                    'price_cents' => $total_int
                ],
                'payer' => [
                    'name' => $name,
                    'email' => $email,
                ],
                'order_id' => $order->id,
                'soft_descriptor_light' => $descriptor
            ];

            if ($cpf_cnpj !== '') {
                $payload_charge['payer']['cpf_cnpj'] = preg_replace('/[^0-9]/', '', $cpf_cnpj);
            }

            // payload_charge['months'] = 2;

            // if ($months > 1 && $months < 12 && $total_int / $months > 500) {
            //     $payload_charge['months'] = $months;
            // }

            $response = IuguRest::request(
                verb: 'POST',
                url: '/charge?api_token=' . $iugu_token,
                headers: $headers,
                body: json_encode($payload_charge),
                timeout: 30
            );

            $json = $response->json ?? null;

            $response_verb = $response->verb;
            $response_url = $response->url;
            $response_errno = $response->errno;
            $response_error = $response->error;
            $response_status_code = $response->status_code;
            $response_time = $response->time;
            $response_body = $response->body;
            $now = today();

            $order->log = json_encode($response);

            $order->transaction_id = $json->invoice_id ?? '';
            $order->save();

            $data = json_encode([
                'timestamp' => time(),
                'gateway' => $gateway,
                'customer' => $customer->id,
                'intent_id' => uniqid(),
                'client_secret' => uniqid(),
                'platform' => (object)[
                    'order_id' => $order->id,
                    'customer_id' => $customer->id,
                ],
                'paid' => $json->status === 'captured',
                'testmode_key' => $testmode_key,
                'card' => [
                    'token' => $card_tokenized
                ]
            ]);

            $data = base64_encode(cryptoJsAesEncrypt(env('AES_HTTP_SECRET'), $data));

            if ($product->payment_type === EProductPaymentType::RECURRING->value) {
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

            $sep = $product->has_upsell ? (str_contains($product->upsell_link, "?") ? "&" : "?") : "?";

            $redirect_url = $product->has_upsell ? $product->upsell_link . $sep . "data=$data" : get_subdomain_serialized("checkout") . "/thanks?id=$order->uuid";

            if ($json->status === 'captured') {
                return Response::json(new ResponseData([
                    'status' => EResponseDataStatus::SUCCESS,
                    'message' => 'Cobrança realizada com sucesso.',
                    'data' => [
                        'url' => $redirect_url
                    ]
                ]), new ResponseStatus('200 OK'));
            } else
                (new Log)->write(base_path('logs/payment.log'), json_encode($json, JSON_PRETTY_PRINT));
                return Response::json(new ResponseData([
                    'status' => EResponseDataStatus::ERROR,
                    'message' => 'Falha no pagamento.'
                ]), new ResponseStatus('400 Bad Request'));
        } catch (\Exception $e)
        {
            (new Log)->write(base_path('logs/payment.log'), json_encode($e, JSON_PRETTY_PRINT));
        }
    }

    public function payPix(Request $request)
    {
        try {
            $body = $request->json();
            $store_user_id = sanitize($body->store_user_id ?? '');
            $product_id = sanitize($body->product_id ?? '');
            $checkout_id = sanitize($body->checkout_id ?? '');
            $name = sanitize($body->name ?? '');
            $email = sanitize($body->email ?? '');

            $first_name = explode(" ", $name)[0];
            $last_name = substr($name, strlen($first_name) + 1, strlen($name));

            $product = Product::where('id', $product_id)->first();

            if (empty($product)) return Response::json(new ResponseData([
                'status' => EResponseDataStatus::ERROR,
                'message' => 'Este produto não foi encontrado.'
            ]), new ResponseStatus('400 Bad Request'));

            $customer = Customer::where('email', $email)->first();
            if (empty($customer)) {
                $customer = new Customer;
                $customer->name = $name;
                $customer->email = $email;
                $customer->user_id = $store_user_id;
                $customer->password = null;
                $customer->access_token = ghash();
                $customer->one_time_access_token = $customer->access_token;
                $customer->status = ECustomerStatus::ACTIVE->value;
                $customer->address_country = 'BR';
                $customer->user_agent = $_SERVER['HTTP_USER_AGENT'];
                $customer->ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'];
                $customer->save();
            }

            $current_order_id = $_SESSION['current_order'];
            $order = Order::find($current_order_id);

            if (empty($order)) return Response::json(new ResponseData([
                'status' => EResponseDataStatus::ERROR,
                'message' => 'Este pedido não foi encontrado.'
            ]), new ResponseStatus('400 Bad Request'));


            $currency_quote = $product->currency == 'brl' ? 1 : (float)get_setting(($product->currency ?: 'usd') . '_brl');
            if ($currency_quote <= 0) return Response::json(new ResponseData([
                'status' => EResponseDataStatus::ERROR,
                'message' => 'A cotação da moeda não pode ser menor que zero.'
            ]), new ResponseStatus('400 Bad Request'));

            $product_price_int = $total_int = intval(($product->price_promo ?: $product->price ?: 0) * 100); // total em inteiro
            $product_price = $product_price_int / 100; // total em decimal

            if ($total_int <= 0) return Response::json(new ResponseData([
                'status' => EResponseDataStatus::ERROR,
                'message' => 'O valor não pode ser menor que zero.'
            ]), new ResponseStatus('400 Bad Request'));

            $total_int = intval($total_int * $currency_quote);
            $total = intval($total_int) / 100;

            $gateway_fee_percent = doubleval(get_setting('gateway_fee_percent')) / 100;
            $gateway_fee_price = doubleval(get_setting('gateway_fee_price'));

            $transaction_fee_extra = doubleval(get_setting('transaction_fee_extra'));
            $platform_fee = doubleval(get_setting('transaction_fee')) / 100;

            $total_gateway = $total * $gateway_fee_percent + $gateway_fee_price;
            $total_seller = $total - $total * $platform_fee - $transaction_fee_extra;
            $total_vendor = $total - $total_seller - $total_gateway;

            $payload_token = [
                'method' => 'PIX',
                'code' => $product_id,
                'amount' => $product_price
            ];

            $headers = ['Content-Type' => 'application/json', 'api-key' => env('NOXPAY_API_KEY')];

            $response = NoxPayRest::request(
                verb: 'POST',
                url: '/payment',
                headers: $headers,
                body: json_encode($payload_token),
            );

            (new Log)->write(base_path('logs/pix_payment.log'), json_encode($response, JSON_PRETTY_PRINT));

            $order->user_id = $store_user_id;
            $order->total = $total;
            $order->total_seller = $total_seller;
            $order->total_vendor = $total_vendor;
            $order->total_gateway = $total_gateway;
            $order->checkout_id = $checkout_id;
            $order->customer_id = $customer->id;
            $order->currency_symbol = $product->currency;
            $order->currency_total = $product_price;
            $order->status = EOrderStatus::PENDING;
            $order->status_details = EOrderStatusDetail::PENDING;
            $order->gateway = 'NoxPay';

            $id_date = date('Ymd');
            $id_price = preg_replace('/\D/', '', $order->total);
            $order->save();

            add_ordermeta($order->id, "customer_name", $customer->name);
            add_ordermeta($order->id, "customer_email", $customer->email);
            add_ordermeta($order->id, "payment_pix_code", $response->json->QRCodeText);
            add_ordermeta($order->id, "payment_pix_image", $response->json->QRCode);

            add_ordermeta($order->id, 'product_id', $product->id);
            add_ordermeta($order->id, 'product_price', $product->price_promo ?: $product->price);
            add_ordermeta($order->id, 'info_total', $product->price_promo ?: $product->price);

            $json = $response->json ?? null;

            $response_verb = $response->verb;
            $response_url = $response->url;
            $response_errno = $response->errno;
            $response_error = $response->error;
            $response_status_code = $response->status_code;
            $response_time = $response->time;
            $response_body = $response->body;
            $now = today();

            $order->log = json_encode($response);

            $order->transaction_id = $json->txid ?? '';
            $order->save();

            if ($product->payment_type === EProductPaymentType::RECURRING->value) {
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
            }

            $sep = $product->has_upsell ? (str_contains($product->upsell_link, "?") ? "&" : "?") : "?";

            $redirect_url = $product->has_upsell ? $product->upsell_link . $sep : get_subdomain_serialized("checkout") . "/pix?id=$order->uuid";

            if ($json->Status === 'WAITING_PAYMENT') {
                return Response::json(new ResponseData([
                    'status' => EResponseDataStatus::SUCCESS,
                    'message' => 'Cobrança realizada com sucesso.',
                    'data' => [
                        'url' => $redirect_url
                    ]
                ]), new ResponseStatus('200 OK'));
            } else
                (new Log)->write(base_path('logs/payment.log'), json_encode($json, JSON_PRETTY_PRINT));
            return Response::json(new ResponseData([
                'status' => EResponseDataStatus::ERROR,
                'message' => 'Falha no pagamento.'
            ]), new ResponseStatus('400 Bad Request'));
        } catch (\Exception $e)
        {
            (new Log)->write(base_path('logs/payment.log'), json_encode($e, JSON_PRETTY_PRINT));
        }
    }
}
