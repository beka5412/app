<?php

namespace Backend\Controllers\Api\App;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Ezeksoft\PHPWriteLog\Log;
use Illuminate\Database\Capsule\Manager as DB;
use Backend\Notifiers\Email\Mailer as Email;
use Backend\Exceptions\Auth\WrongPasswordException;
use Backend\Exceptions\User\{
    UserNotFoundException,
    OneSignalPlayerIDEmptyException,
    OneSignalPlayerIDAlreadyExistsException,
    AuthException
};
use Backend\Exceptions\Upsell\UpsellNotFoundException;
use Backend\Exceptions\Product\ProductNotFoundException;
use Backend\Exceptions\Customer as CustomerExceptions;
use Backend\Exceptions\Order\OrderNotFoundException;
use Backend\Exceptions\Product\NoProductPermissionException;
use Backend\Exceptions\Pagarme as PagarmeExeptions;
use Backend\Enums\Order\{EOrderStatus, EOrderStatusDetail};
use UnexpectedValueException;
use Backend\Attributes\Route;
use Backend\Models\User;
use Backend\Models\Product;
use Backend\Models\Order;
use Backend\Models\Balance;
use Backend\Models\OrderMeta;
use Backend\Models\PagarmeCustomer;
use Backend\Models\Customer;
use Backend\Models\Upsell;
use Backend\Models\ProductLink;
use Backend\Services\PagarMe\PagarMe;

class IndexController
{
    /**
     * Instancia herdada do nucleo da aplicacao
     *
     * @access public
     * @var App $application
     */
    public App $application;

    /**
     * Chave de autorizacao das requisicoes para esta api
     * 
     * @access protected
     * @var String $token
     */
    protected $token = '';

    /**
     * Chave para encriptar token jwt
     * 
     * @access protected
     * @var String $secret_key
     */
    protected $secret_key = '';

    /**
     * Seta variaveis
     *
     * @access public
     * @param App $application
     */
    public function __construct(App $application)
    {
        $this->application = $application;
        $this->secret_key = env('APP_MOBILE_SECRET');
        $this->token = env('APP_MOBILE_TOKEN');
    }

    /**
     * Checa se a requisicao esta autenticada
     * 
     * @access protected
     * @return Object|Array     Status da tentativa de autenticacao
     * @return void
     */
    protected function auth(): Object|array
    {
        $headers = getallheaders();
        $auth = $headers['Authorization'] ?? '';

        if (empty($auth)) return (object) ['status' => 'error', 'message' => 'Sem autorização para esta requisição.'];

        $token = str_replace("Bearer ", "", $auth);

        if ($token <> $this->token)
            return (object) ['status' => 'error', 'message' => 'Credenciais inválidas para a API.'];

        return (object) ['status' => 'success', 'message' => 'Autenticado com sucesso.'];
    }

    /**
     * Finaliza a execucao caso a requisicao nao seja autenticada
     * 
     * @access protected
     * @return void
     */
    protected function middleware()
    {
        header("Content-Type: application/json");
        $auth = $this->auth();

        if ($auth->status == 'error')
        {
            die(Response::json($auth));
        }
    }

    /**
     * Codifica um array em jwt
     *
     * @access protected
     * @param Array $data   Conteudo a ser codificado
     * @param String $key   Senha para decodificar o conteudo
     * @return void
     */
    protected function jwtEncode(array $data, String $key)
    {
        return JWT::encode($data, $key, 'HS256');
    }

    /**
     * Decodifica jwt
     *
     * @access protected
     * @param Array $jwt    Token gerado
     * @param String $key   Senha para decodificar o conteudo
     * @return String
     */
    protected function jwtDecode($jwt, $key)
    {
        return JWT::decode($jwt, new Key($key, 'HS256'));
    }

    /**
     * Verifica se o jwt eh valido
     *
     * @access protected
     * @param Request $request
     * @throws \Exception
     * @throws \UnexpectedValueException
     * @throws \Firebase\JWT\SignatureInvalidException
     * @return void
     */
    protected function validateJwt(Request $request)
    {
        $jwt = $request->header('User-Jwt');

        $response = ['status' => 'pending'];

        try
        {
            $payload = $this->jwtDecode($jwt, $this->secret_key);
            $response = ['status' => 'success', 'payload' => $payload];
        }

        catch (UnexpectedValueException)
        {
            $response = ['status' => 'error', 'message' => 'Este jwt não foi gerado com a chave desta api.'];
        }

        catch (SignatureInvalidException)
        {
            $response = ['status' => 'error', 'message' => 'Assinatura do jwt inválida.'];
        }

        catch (\Exception)
        {
            $response = ['status' => 'error', 'message' => 'Token jwt inválido.'];
        }

        $response = (object) $response;

        if ($response->status === 'error')
        {
            die(Response::json($response));
        }

        return (object) [
            "jwt" => $jwt,
            "payload" => $response->payload
        ];
    }

    /**
     * Tenta logar
     * 
     * @param \Backend\Http\Request $request
     * @throws \Exception
     * @throws \UnexpectedValueException
     * @throws \Firebase\JWT\SignatureInvalidException
     * @throws \Backend\Exceptions\Auth\WrongPasswordException
     */
    public function login(Request $request)
    {
        $this->middleware();

        $body = $request->json();
        $attempt_jwt = $body->attempt_jwt ?? '';

        $response = [];

        try
        {
            // $body = $this->jwtDecode($attempt_jwt, $this->token);
            $login = $body->login ?? '';
            $password = $body->password ?? '';

            $user = User::where('email', $login)->where('password', hash_make($password))->first();
            if (empty($user)) throw new WrongPasswordException;

            $days = 7;
            $duration = $days * 60 * 60 * 24;

            $data = [
                'iss' => 'RocketPays', // nome do emissor
                'iat' => time(), // data de emissao
                'nbf' => time(), // data que comeca a ser valido
                'exp' => strtotime(date('Y-m-d H:i:s') . " + $days days"), // data que expira
                "user_id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,
                "status" => $user->status,
                "created_at" => $user->created_at,
            ];

            $user->jwt = $this->jwtEncode($data, $this->secret_key);
            $user->save();

            $response = [
                'status' => 'success',
                'jwt' => $user->jwt,
                'expire_at' => $duration,
                'message' => 'Token de seção gerado com sucesso.'
            ];
        }

        catch (WrongPasswordException)
        {
            $response = ['status' => 'error', 'message' => 'Login ou senha incorreto.'];
        }

        catch (UnexpectedValueException)
        {
            $response = ['status' => 'error', 'message' => 'Este jwt não foi gerado com a chave desta api.'];
        }

        catch (SignatureInvalidException)
        {
            $response = ['status' => 'error', 'message' => 'Assinatura do jwt inválida.'];
        }

        catch (\Exception)
        {
            $response = ['status' => 'error', 'message' => 'Token jwt inválido.'];
        }

        finally
        {
            Response::json($response);
        }
    }

    public function test(Request $request)
    {
        $body = $request->json();
        // print_r($this->jwtDecode($body->jwt));
        print_r(
            $this->jwtEncode([
                'iss' => 'AppRocketPays', // nome do emissor
                'iat' => time(), // data de emissao
                'nbf' => time(), // data que comeca a ser valido
                'exp' => strtotime(date('Y-m-d H:i:s') . ' 1 days'), // data que expira
                "login" => $body?->login ?? '',
                "password" => $body?->password ?? ''
            ], $this->token)
        );
    }

    public function test_onesignal(Request $request)
    {
        $onesignal = new \Backend\Services\OneSignal\OneSignal;
        $onesignal->setTitle("Título");
        $onesignal->setDescription("Descrição");
        $onesignal->setData(["dados" => "personalizados"]);
        $onesignal->addExternalUserID("quielbala@gmail.com");
        $result = $onesignal->pushNotification();

        print_r($result);
    }

    /**
     * Recupera informacoes do usuario
     *
     * @param Request $request
     * @throws \Backend\Exceptions\User\UserNotFoundException
     * @return void
     */
    public function user(Request $request)
    {
        $this->middleware();
        $payload = $this->validateJwt($request);

        $response = ['status' => 'pending', 'message' => 'Solicitação incompleta.'];

        try
        {
            $user = User::where('jwt', $payload->jwt)->with('bank_account', 'balance')->first();
            if (empty($user)) return throw new UserNotFoundException;

            $count_orders = Order::where('user_id', $user->id)->count() ?? 0;

            $count_pix_sales = 0;
            $count_billet_sales = 0;
            $count_credit_card_sales = 0;
            $count_approved_sales = 0;
            $total_approved = 0;
            $count_pending_sales = 0;
            $total_pending = 0;
            $count_canceled_sales = 0;
            $total_canceled = 0;

            $aux = Order::select('id', 'total')->where('user_id', $user->id)->where('status', EOrderStatus::PENDING->value)->get();
            foreach ($aux as $aux1)
            {
                $total_pending += $aux1->total;
                $count_pending_sales++;
            }
            unset($aux);
            unset($aux1);

            $aux = Order::select('id', 'total')->where('user_id', $user->id)->where('status', EOrderStatus::CANCELED->value)->get();
            foreach ($aux as $aux1)
            {
                $total_canceled += $aux1->total;
                $count_canceled_sales++;
            }
            unset($aux);
            unset($aux1);

            $aux = Order::select('id', 'total')->where('user_id', $user->id)->where('status', EOrderStatus::APPROVED->value)->get();
            foreach ($aux as $order)
            {
                $info_payment_method = $order->meta("info_payment_method");
                if ($info_payment_method == "pix") $count_pix_sales++;
                if ($info_payment_method == "billet") $count_billet_sales++;
                if ($info_payment_method == "credit_card") $count_credit_card_sales++;
                $count_approved_sales++;
                $total_approved += $order->total;
            }

            $aux = [];
            $sales_last_30_days = Order::select('id', DB::raw('sum(total) total'), DB::raw('date(created_at) as `date`'), DB::raw('day(created_at) as `day`'))
                ->where('user_id', $user->id)->where('status', EOrderStatus::APPROVED->value)
                ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime(today() . " - 30 days")))->groupBy('date')->get();
            foreach ($sales_last_30_days as $sale)
            {
                $aux1 = [
                    "total" => [
                        "display" => "R$ " . currency($sale->total),
                        "value" => $sale->total
                    ],
                    "date" => [
                        "display" => $sale->day,
                        "value" => $sale->date,
                    ]
                ];

                $aux[] = $aux1;
            }
            $sales_last_30_days = $aux;
            unset($aux);

            $response = [
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,
                "status" => $user->status,
                "bank_account" => $user->bank_account,
                "balance" => $user->balance,
                "created_at" => $user->created_at,
                "updated_at" => $user->updated_at,
                "stats" => [
                    "sales" => [
                        "approved" => [
                            "total" => [
                                "display" => "R$ " . currency($total_approved),
                                "value" => $total_approved
                            ],
                            "qty" => $count_approved_sales
                        ],
                        "pending" => [
                            "total" => [
                                "display" => "R$ " . currency($total_pending),
                                "value" => $total_pending
                            ],
                            "qty" => $count_pending_sales
                        ],
                        "canceled" => [
                            "total" => [
                                "display" => "R$ " . currency($total_canceled),
                                "value" => $total_canceled
                            ],
                            "qty" => $count_canceled_sales
                        ]
                    ],
                    "conversion" => [
                        "value" => $count_orders ? $count_approved_sales / $count_orders : 0,
                        "display" => ($count_orders ? currency($count_approved_sales / $count_orders * 100) : 0) . "%",
                    ],
                    "orders" => $count_orders,
                    "sales_last_30_days" => $sales_last_30_days
                ]
            ];
        }

        catch (UserNotFoundException)
        {
            $response = ['status' => 'error', 'message' => 'Seção de usuário não encontrada.'];
        }

        finally
        {
            Response::json($response);
        }
    }

    /**
     * Recupera lista de produtos
     *
     * @param Request $request
     * @throws \Backend\Exceptions\User\UserNotFoundException
     * @return void
     */
    public function products(Request $request)
    {
        $this->middleware();
        $payload = $this->validateJwt($request);

        $site_checkout = get_subdomain_serialized('checkout');

        $page = $request->query('page') ?: 1;
        $per_page = 10;

        $response = ['status' => 'pending', 'message' => 'Solicitação incompleta.'];

        try
        {
            $user = User::where('jwt', $payload->jwt)->first();
            if (empty($user)) return throw new UserNotFoundException;

            $products = Product::where('user_id', $user->id)->with('checkouts')->orderBy('id', 'DESC')
                ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);
            $count = Product::where('user_id', $user->id)->count();

            $list = [];

            foreach ($products as $product)
            {
                $item = [
                    "id" => $product->id,
                    "user_id" => $product->user_id,
                    "name" => $product->name,
                    "price" => $product->price,
                    "publish_status" => $product->publish_status,
                    "created_at" => $product->created_at,
                    "updated_at" => $product->updated_at,
                    "sku" => $product->sku,
                    "description" => $product->description,
                    "is_free" => $product->is_free,
                    "price_promo" => $product->price_promo,
                    "stock_qty" => $product->stock_qty,
                    "stock_control" => $product->stock_control,
                    "landing_page" => $product->landing_page,
                    "support_email" => $product->support_email,
                    "author" => $product->author,
                    "warranty_time" => $product->warranty_time,
                    "type" => $product->type,
                    "payment_type" => $product->payment_type,
                    "delivery" => $product->delivery,
                    "image" => site_url() . "$product->image",
                    "category_id" => $product->category_id,
                    "attachment_url" => $product->attachment_url,
                    "attachment_file" => site_url() . "$product->attachment_file",
                    "pix_enabled" => $product->pix_enabled,
                    "credit_card_enabled" => $product->credit_card_enabled,
                    "billet_enabled" => $product->billet_enabled,
                    "pix_discount_enabled" => $product->pix_discount_enabled,
                    "credit_card_discount_enabled" => $product->credit_card_discount_enabled,
                    "billet_discount_enabled" => $product->billet_discount_enabled,
                    "pix_discount_amount" => $product->pix_discount_amount,
                    "credit_card_discount_amount" => $product->credit_card_discount_amount,
                    "billet_discount_amount" => $product->billet_discount_amount,
                    "max_installments" => $product->max_installments,
                    "pix_thanks_page_enabled" => $product->pix_thanks_page_enabled,
                    "pix_thanks_page_url" => $product->pix_thanks_page_url,
                    "credit_card_thanks_page_enabled" => $product->credit_card_thanks_page_enabled,
                    "credit_card_thanks_page_url" => $product->credit_card_thanks_page_url,
                    "billet_thanks_page_enabled" => $product->billet_thanks_page_enabled,
                    "billet_thanks_page_url" => $product->billet_thanks_page_url
                ];

                $item["links"] = [];
                $default_checkout = null;

                if (!empty($product->checkouts))
                {
                    $item["checkouts"] = [];
                    foreach ($product->checkouts as $checkout)
                    {
                        $checkout_item = [
                            "id" => $checkout->id,
                            "product_id" => $checkout->product_id,
                            "checkout_theme_id" => $checkout->checkout_theme_id,
                            "sku" => $checkout->sku,
                            "name" => $checkout->name,
                            "description" => $checkout->description,
                            "top_banner" => $checkout->top_banner,
                            "sidebar_banner" => $checkout->sidebar_banner,
                            "footer_banner" => $checkout->footer_banner,
                            "status" => $checkout->status,
                            "created_at" => $checkout->created_at,
                            "updated_at" => $checkout->updated_at,
                            "user_id" => $checkout->user_id,
                            "dark_mode" => $checkout->dark_mode,
                            "logo" => $checkout->logo,
                            "favicon" => $checkout->favicon,
                            "top_color" => $checkout->top_color,
                            "primary_color" => $checkout->primary_color,
                            "secondary_color" => $checkout->secondary_color,
                            "countdown_enabled" => $checkout->countdown_enabled,
                            "countdown_text" => $checkout->countdown_text,
                            "countdown_time" => $checkout->countdown_time,
                            "pix_enabled" => $checkout->pix_enabled,
                            "credit_card_enabled" => $checkout->credit_card_enabled,
                            "billet_enabled" => $checkout->billet_enabled,
                            "pix_discount_enabled" => $checkout->pix_discount_enabled,
                            "credit_card_discount_enabled" => $checkout->credit_card_discount_enabled,
                            "billet_discount_enabled" => $checkout->billet_discount_enabled,
                            "pix_discount_amount" => $checkout->pix_discount_amount,
                            "credit_card_discount_amount" => $checkout->credit_card_discount_amount,
                            "billet_discount_amount" => $checkout->billet_discount_amount,
                            "max_installments" => $checkout->max_installments,
                            "pix_thanks_page_enabled" => $checkout->pix_thanks_page_enabled,
                            "pix_thanks_page_url" => $checkout->pix_thanks_page_url,
                            "credit_card_thanks_page_enabled" => $checkout->credit_card_thanks_page_enabled,
                            "credit_card_thanks_page_url" => $checkout->credit_card_thanks_page_url,
                            "billet_thanks_page_enabled" => $checkout->billet_thanks_page_enabled,
                            "billet_thanks_page_url" => $checkout->billet_thanks_page_url,
                            "sales" => $checkout->sales,
                            "notifications_enabled" => $checkout->notifications_enabled,
                            "notification_interested24_enabled" => $checkout->notification_interested24_enabled,
                            "notification_interested24_number" => $checkout->notification_interested24_number,
                            "notification_interested_weekly_enabled" => $checkout->notification_interested_weekly_enabled,
                            "notification_interested_weekly_number" => $checkout->notification_interested_weekly_number,
                            "notification_order24_enabled" => $checkout->notification_order24_enabled,
                            "notification_order24_number" => $checkout->notification_order24_number,
                            "notification_order_weekly_enabled" => $checkout->notification_order_weekly_enabled,
                            "notification_order_weekly_number" => $checkout->notification_order_weekly_number,
                            "whatsapp_enabled" => $checkout->whatsapp_enabled,
                            "whatsapp_number" => $checkout->whatsapp_number,
                            "default" => $checkout->default
                        ];

                        if ($checkout->default) $default_checkout = $checkout;

                        $item["checkouts"][] = $checkout_item;
                        $item["links"][] = "$site_checkout/$checkout->sku";


                        if (!empty($product->product_links))
                        {
                            foreach ($product->product_links as $product_link)
                            {
                                $item["links"][] = "$site_checkout/$checkout->sku/$product_link->slug";
                            }
                        }
                    }
                }

                $default_sku = $default_checkout->sku ?: $product->sku;

                $list[] = $item;
            }

            $response = [
                "data" => $list,
                "pagination" => [
                    "current_page" => $page,
                    "total" => $count,
                    "per_page" => $per_page,
                ]
            ];
        }

        catch (UserNotFoundException)
        {
            $response = ['status' => 'error', 'message' => 'Seção de usuário não encontrada.'];
        }

        finally
        {
            Response::json($response);
        }
    }

    /**
     * Recupera um produto
     *
     * @param Request $request
     * @throws \Backend\Exceptions\User\UserNotFoundException
     * @return void
     */
    public function product(Request $request, $product_id)
    {
        $this->middleware();
        $payload = $this->validateJwt($request);

        $site_checkout = get_subdomain_serialized('checkout');

        $response = ['status' => 'pending', 'message' => 'Solicitação incompleta.'];

        try
        {
            $user = User::where('jwt', $payload->jwt)->first();
            if (empty($user)) return throw new UserNotFoundException;

            $product = Product::where('user_id', $user->id)->where(
                'id',
                $product_id
            )->with('checkouts')->with('product_links')->orderBy('id', 'DESC')->first();

            if (empty($product)) throw new NoProductPermissionException;

            $item = [
                "id" => $product->id,
                "user_id" => $product->user_id,
                "name" => $product->name,
                "price" => $product->price,
                "publish_status" => $product->publish_status,
                "created_at" => $product->created_at,
                "updated_at" => $product->updated_at,
                "sku" => $product->sku,
                "description" => $product->description,
                "is_free" => $product->is_free,
                "price_promo" => $product->price_promo,
                "stock_qty" => $product->stock_qty,
                "stock_control" => $product->stock_control,
                "landing_page" => $product->landing_page,
                "support_email" => $product->support_email,
                "author" => $product->author,
                "warranty_time" => $product->warranty_time,
                "type" => $product->type,
                "payment_type" => $product->payment_type,
                "delivery" => $product->delivery,
                "image" => site_url() . "$product->image",
                "category_id" => $product->category_id,
                "attachment_url" => $product->attachment_url,
                "attachment_file" => site_url() . "$product->attachment_file",
                "pix_enabled" => $product->pix_enabled,
                "credit_card_enabled" => $product->credit_card_enabled,
                "billet_enabled" => $product->billet_enabled,
                "pix_discount_enabled" => $product->pix_discount_enabled,
                "credit_card_discount_enabled" => $product->credit_card_discount_enabled,
                "billet_discount_enabled" => $product->billet_discount_enabled,
                "pix_discount_amount" => $product->pix_discount_amount,
                "credit_card_discount_amount" => $product->credit_card_discount_amount,
                "billet_discount_amount" => $product->billet_discount_amount,
                "max_installments" => $product->max_installments,
                "pix_thanks_page_enabled" => $product->pix_thanks_page_enabled,
                "pix_thanks_page_url" => $product->pix_thanks_page_url,
                "credit_card_thanks_page_enabled" => $product->credit_card_thanks_page_enabled,
                "credit_card_thanks_page_url" => $product->credit_card_thanks_page_url,
                "billet_thanks_page_enabled" => $product->billet_thanks_page_enabled,
                "billet_thanks_page_url" => $product->billet_thanks_page_url
            ];

            $item["links"] = [];
            $default_checkout = null;

            if (!empty($product->checkouts))
            {
                $item["checkouts"] = [];
                foreach ($product->checkouts as $checkout)
                {
                    $checkout_item = [
                        "id" => $checkout->id,
                        "product_id" => $checkout->product_id,
                        "checkout_theme_id" => $checkout->checkout_theme_id,
                        "sku" => $checkout->sku,
                        "name" => $checkout->name,
                        "description" => $checkout->description,
                        "top_banner" => $checkout->top_banner,
                        "sidebar_banner" => $checkout->sidebar_banner,
                        "footer_banner" => $checkout->footer_banner,
                        "status" => $checkout->status,
                        "created_at" => $checkout->created_at,
                        "updated_at" => $checkout->updated_at,
                        "user_id" => $checkout->user_id,
                        "dark_mode" => $checkout->dark_mode,
                        "logo" => $checkout->logo,
                        "favicon" => $checkout->favicon,
                        "top_color" => $checkout->top_color,
                        "primary_color" => $checkout->primary_color,
                        "secondary_color" => $checkout->secondary_color,
                        "countdown_enabled" => $checkout->countdown_enabled,
                        "countdown_text" => $checkout->countdown_text,
                        "countdown_time" => $checkout->countdown_time,
                        "pix_enabled" => $checkout->pix_enabled,
                        "credit_card_enabled" => $checkout->credit_card_enabled,
                        "billet_enabled" => $checkout->billet_enabled,
                        "pix_discount_enabled" => $checkout->pix_discount_enabled,
                        "credit_card_discount_enabled" => $checkout->credit_card_discount_enabled,
                        "billet_discount_enabled" => $checkout->billet_discount_enabled,
                        "pix_discount_amount" => $checkout->pix_discount_amount,
                        "credit_card_discount_amount" => $checkout->credit_card_discount_amount,
                        "billet_discount_amount" => $checkout->billet_discount_amount,
                        "max_installments" => $checkout->max_installments,
                        "pix_thanks_page_enabled" => $checkout->pix_thanks_page_enabled,
                        "pix_thanks_page_url" => $checkout->pix_thanks_page_url,
                        "credit_card_thanks_page_enabled" => $checkout->credit_card_thanks_page_enabled,
                        "credit_card_thanks_page_url" => $checkout->credit_card_thanks_page_url,
                        "billet_thanks_page_enabled" => $checkout->billet_thanks_page_enabled,
                        "billet_thanks_page_url" => $checkout->billet_thanks_page_url,
                        "sales" => $checkout->sales,
                        "notifications_enabled" => $checkout->notifications_enabled,
                        "notification_interested24_enabled" => $checkout->notification_interested24_enabled,
                        "notification_interested24_number" => $checkout->notification_interested24_number,
                        "notification_interested_weekly_enabled" => $checkout->notification_interested_weekly_enabled,
                        "notification_interested_weekly_number" => $checkout->notification_interested_weekly_number,
                        "notification_order24_enabled" => $checkout->notification_order24_enabled,
                        "notification_order24_number" => $checkout->notification_order24_number,
                        "notification_order_weekly_enabled" => $checkout->notification_order_weekly_enabled,
                        "notification_order_weekly_number" => $checkout->notification_order_weekly_number,
                        "whatsapp_enabled" => $checkout->whatsapp_enabled,
                        "whatsapp_number" => $checkout->whatsapp_number,
                        "default" => $checkout->default
                    ];

                    if ($checkout->default) $default_checkout = $checkout;

                    $item["checkouts"][] = $checkout_item;
                    $item["links"][] = "$site_checkout/$checkout->sku";

                    if (!empty($product->product_links))
                    {
                        foreach ($product->product_links as $product_link)
                        {
                            $item["links"][] = "$site_checkout/$checkout->sku/$product_link->slug";
                        }
                    }
                }
            }

            $default_sku = $default_checkout->sku ?: $product->sku;

            $response = $item;
        }

        catch (NoProductPermissionException)
        {
            $response = ['status' => 'error', 'message' => 'Você não tem permissão para acessar este produto.'];
        }

        catch (UserNotFoundException)
        {
            $response = ['status' => 'error', 'message' => 'Seção de usuário não encontrada.'];
        }

        finally
        {
            Response::json($response);
        }
    }

    /**
     * Recupera lista de pedidos
     *
     * @param Request $request
     * @throws \Backend\Exceptions\User\UserNotFoundException
     * @return void
     */
    public function orders(Request $request)
    {
        $this->middleware();
        $payload = $this->validateJwt($request);

        $page = $request->query('page') ?: 1;
        $per_page = 10;

        $response = ['status' => 'pending', 'message' => 'Solicitação incompleta.'];

        try
        {
            $user = User::where('jwt', $payload->jwt)->first();
            if (empty($user)) return throw new UserNotFoundException;

            $orders = Order::where('user_id', $user->id)->orderBy('id', 'DESC')
                ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);
            $count = Order::where('user_id', $user->id)->count();

            $list = [];

            foreach ($orders as $order)
            {
                $item = [
                    "id" => $order->id,
                    "uuid" => $order->uuid,
                    "total" => $order->total,
                    "created_at" => $order->created_at,
                    "updated_at" => $order->updated_at,
                    "transaction_id" => $order->transaction_id,
                    "status" => $order->status,
                    "status_details" => $order->status_details,
                    "user_id" => $order->user_id,
                    "customer_id" => $order->customer_id,
                    "seller_was_credited" => $order->seller_was_credited,
                    "seller_credited_at" => $order->seller_credited_at,
                    "queue_seller_credit" => $order->queue_seller_credit,
                    "total_seller" => $order->total_seller,
                    "total_vendor" => $order->total_vendor,
                ];

                $metas = OrderMeta::where('order_id', $order->id)->get();
                if (!empty($metas)) $item["meta"] = [];
                foreach ($metas as $meta)
                {
                    $item["meta"][$meta->name] = $meta->value;
                    if ($meta->name == "product_id")
                    {
                        $product = Product::where('id', $meta->value)->first();
                        if (!empty($product)) $item["meta"]["product"] = (object) [
                            "name" => $product->name,
                            "image" => site_url() . "$product->image"
                        ];
                    }
                }

                $list[] = $item;
            }

            $response = [
                "data" => $list,
                "pagination" => [
                    "current_page" => $page,
                    "total" => $count,
                    "per_page" => $per_page,
                ]
            ];
        }

        catch (UserNotFoundException)
        {
            $response = ['status' => 'error', 'message' => 'Seção de usuário não encontrada.'];
        }

        finally
        {
            Response::json($response);
        }
    }

    /**
     * Recupera lista de pedidos
     *
     * @param Request $request
     * @param Mixed $order_id
     * @throws \Backend\Exceptions\User\UserNotFoundException
     * @return void
     */
    public function order(Request $request, Mixed $order_id)
    {
        $this->middleware();
        $payload = $this->validateJwt($request);

        $page = $request->query('page') ?: 1;
        $per_page = 10;

        $response = ['status' => 'pending', 'message' => 'Solicitação incompleta.'];

        try
        {
            $user = User::where('jwt', $payload->jwt)->first();
            if (empty($user)) return throw new UserNotFoundException;

            $order = Order::where('user_id', $user->id)->where('id', $order_id)->orderBy('id', 'DESC')->first();
            if (empty($order)) throw new OrderNotFoundException;

            $item = [
                "id" => $order->id,
                "uuid" => $order->uuid,
                "total" => $order->total,
                "created_at" => $order->created_at,
                "updated_at" => $order->updated_at,
                "transaction_id" => $order->transaction_id,
                "status" => $order->status,
                "status_details" => $order->status_details,
                "user_id" => $order->user_id,
                "customer_id" => $order->customer_id,
                "seller_was_credited" => $order->seller_was_credited,
                "seller_credited_at" => $order->seller_credited_at,
                "queue_seller_credit" => $order->queue_seller_credit,
                "total_seller" => $order->total_seller,
                "total_vendor" => $order->total_vendor,
            ];

            $metas = OrderMeta::where('order_id', $order->id)->get();
            if (!empty($metas)) $item["meta"] = [];
            foreach ($metas as $meta)
            {
                $item["meta"][$meta->name] = $meta->value;
                if ($meta->name == "product_id")
                {
                    $product = Product::where('id', $meta->value)->first();
                    if (!empty($product)) $item["meta"]["product"] = (object) [
                        "name" => $product->name,
                        "image" => site_url() . "$product->image"
                    ];
                }
            }

            $response = $item;
        }

        catch (OrderNotFoundException)
        {
            $response = ['status' => 'error', 'message' => 'Pedido não encontrado.'];
        }

        catch (UserNotFoundException)
        {
            $response = ['status' => 'error', 'message' => 'Seção de usuário não encontrada.'];
        }

        finally
        {
            Response::json($response);
        }
    }


    /**
     * Recupera lista de pedidos
     *
     * @param Request $request
     * @throws \Backend\Exceptions\User\UserNotFoundException
     * @return void
     */
    public function update_user(Request $request)
    {
        $this->middleware();
        $payload = $this->validateJwt($request);

        $body = $request->json();

        try
        {
            $user = User::where('jwt', $payload->jwt)->first();
            if (empty($user)) return throw new UserNotFoundException;

            $player_id = $body->player_id ?? '';

            if (empty($player_id)) throw new OneSignalPlayerIDEmptyException;

            if (User::where('onesignal_player_id', $player_id)->exists())
                throw new OneSignalPlayerIDAlreadyExistsException;

            $user->onesignal_player_id = $player_id;
            $user->save();

            $response = ['status' => 'success', 'message' => 'Usuário atualizado com sucesso.'];
        }

        catch (OneSignalPlayerIDEmptyException)
        {
            $response = ['status' => 'error', 'message' => 'O player_id não pode estar vazio.'];
        }

        catch (OneSignalPlayerIDAlreadyExistsException)
        {
            $response = ['status' => 'error', 'message' => 'Este player_id já foi atribuido à um usuário.'];
        }

        catch (UserNotFoundException)
        {
            $response = ['status' => 'error', 'message' => 'Seção de usuário não encontrada.'];
        }

        finally
        {
            Response::json($response);
        }
    }

    /**
     * @param Request $request
     * @throws CustomerExceptions\AuthException
     * @throws CustomerExceptions\TokenExpiredException
     * @throws PagarmeExeptions\CustomerNotFoundException
     * @throws OrderNotFoundException
     * @throws UpsellNotFoundException
     * @throws PagarmeExeptions\EmptyCardsException
     * @return void
     */
    #[Route(verb: 'POST', uri: '/api/app/upsell/pay')]
    public function upsellPay(Request $request)
    {
        header("Content-Type: application/json");

        $pagarme = new Pagarme;
        $response = [];

        try
        {
            $body = $request->json();
            $payment_method = $body->paymentMethod ?? '';
            $installments = $body->installments ?? '';
            $token = $body->token ?? '';
            $order_id = $body->order_id ?? '';
            $upsell_id = $body->upsell_id ?? '';
            $price_var = $body->price_var ?? '';

            $customer = Customer::where('upsell_token', $token)->first();
            if (empty($customer)) throw new CustomerExceptions\AuthException;

            if (strtotime($customer->upsell_token_at ?? 0) < strtotime(today()))
                throw new CustomerExceptions\TokenExpiredException;

            $pm_customer = PagarmeCustomer::where('customer_id', $customer->id)->orderBy('id', 'DESC')->first();
            if (empty($pm_customer)) throw new PagarmeExeptions\CustomerNotFoundException;

            $prev_order = Order::find($order_id);
            if (empty($prev_order)) throw new OrderNotFoundException;

            $upsell = Upsell::where('id', $upsell_id)->with('product')->first();
            if (empty($upsell)) throw new UpsellNotFoundException;

            $product = $upsell->product;
            if (empty($product)) throw new ProductNotFoundException;

            $product_link = ProductLink::find($price_var);
            $alt_price = $product_link->amount ?? 0;
            $total = $alt_price ?: $product->price_promo ?: $product->price;

            $order = new Order;
            $order->uuid = uuid();
            $order->total_seller = 0;
            $order->total_vendor = 0;
            $order->status = EOrderStatus::PENDING;
            $order->status_details = EOrderStatusDetail::PENDING;
            $order->save();

            $total_without_cc_fee = $total;
            $fee_percent = doubleval(get_setting('transaction_fee')) / 100; // porcentagem que o vendor cobra por transacao
            $fee_rs = doubleval(get_setting('transaction_fee_extra')); // taxa extra em reais que o vendor cobra por transacao
            $vendor_fee = $total_without_cc_fee * $fee_percent; // quanto o vendor vai receber em reais
            $fee_rs_apply = $total >= 3 ? $fee_rs : 0; // se a total deu um valor minimo para aplicar as taxas, usar as taxas, caso contrario, ficar 0

            switch ($payment_method)
            {
                case 'credit_card':
                    $order->total_seller = ($total_without_cc_fee - $fee_rs) - $vendor_fee;
                    $order->total_seller = $order->total_seller > 0 ? $order->total_seller : 0;
                    $order->total_vendor = $vendor_fee;
                    $order->total_vendor = $order->total_vendor > 0 ? $order->total_vendor : 0;
                    break;

                case 'pix':
                    $order->total_seller = ($total - $fee_rs) - $vendor_fee;
                    $order->total_seller = $order->total_seller > 0 ? $order->total_seller : 0;
                    $order->total_vendor = $vendor_fee;
                    $order->total_vendor = $order->total_vendor > 0 ? $order->total_vendor : 0;
                    break;

                case 'billet':
                    $order->total_seller = ($total - $fee_rs) - $vendor_fee;
                    $order->total_seller = $order->total_seller > 0 ? $order->total_seller : 0;
                    $order->total_vendor = $vendor_fee;
                    $order->total_vendor = $order->total_vendor > 0 ? $order->total_vendor : 0;
                    break;
            }

            $data_customer =
                [
                    "name" => $customer->name,
                    "email" => $customer->email,
                    "cpf_cnpj" => $customer->doc
                ];
            $data_customer_extra =
                [
                    "phone" => preg_replace("/\D/", "", $customer->phone)
                ];
            $data_address =
                [
                    "billing_address" =>
                    [
                        "street" => $customer->address_street,
                        "number" => $customer->address_number,
                        "district" => $customer->address_district,
                        "complement" => $customer->address_complement,
                        "city" => $customer->address_city,
                        "state" => $customer->address_state,
                        "zipcode" => $customer->address_zipcode,
                    ]
                ];
            $data_info =
                [
                    "total" => $total ?? 0,
                    "payment_method" => $payment_method,
                ];
            $data_cart =
                [
                    "customer" => $data_customer + $data_customer_extra + $data_address,
                    "product" => json_decode(json_encode($product))
                ];
            $data = $data_info + $data_cart;

            if ($payment_method == 'credit_card')
                $data["installments"] = $installments ?? 1;

            $data["total_seller"] = $order->total_seller;
            $data["total_vendor"] = $order->total_vendor;

            $card = null;
            if ($payment_method == 'credit_card')
            {
                $cards = json_decode($pagarme->getCards($pm_customer->pm_customer_id) ?: '{}')->data ?? [];

                // encontra primeiro cartao ativo
                $found = '';
                foreach ($cards as $card) if (empty($found) && $card->status == "active") $found = $card;

                if (empty($found)) throw new PagarmeExeptions\EmptyCardsException;
                $card = $found;

                $data["card_id"] = $card->id;
            }

            $payment = json_decode($result = match ($payment_method)
            {
                'credit_card' => $pagarme->creditCard($data),
                'pix' => $pagarme->pix($data),
                'billet' => $pagarme->billet($data),
            });
            $payment = new class
            {
            };
            $payment->total = $total;


            $order->customer_id = $customer->id;
            $order->total = $total;
            $order->save();

            $log = new Log;
            $log->write(base_path('logs/pagarme_request_upsell.log'), json_encode($payment));

            $response = ["status" => "success", "message" => "Compra realizada com sucesso.", "data" => $payment];
        }

        catch (CustomerExceptions\AuthException)
        {
            $response = ["status" => "error", "message" => "Usuário não autenticado."];
        }

        catch (CustomerExceptions\TokenExpiredException)
        {
            $response = ["status" => "error", "message" => "Sua seção expirou."];
        }

        catch (PagarmeExeptions\CustomerNotFoundException)
        {
            $response = ["status" => "error", "message" => "Você ainda não fez nenhuma compra ou não encontramos o seu usuário no gateway."
                . " Você pode resolver isto gerando um pix ou boleto em algum checkout."];
        }

        catch (OrderNotFoundException)
        {
            $response = ["status" => "error", "message" => "Você deve realizar uma compra via checkout antes de tentar por este botão."];
        }

        catch (UpsellNotFoundException)
        {
            $response = ["status" => "error", "message" => "Oferta não encontrada."];
        }

        catch (PagarmeExeptions\EmptyCardsException)
        {
            $response = ["status" => "error", "message" => "Você não tem cartão cadastrado."];
        }

        catch (ProductNotFoundException)
        {
            $response = ["status" => "error", "message" => "O produto não foi encontrado."];
        }

        // catch (\Throwable $th)
        // {
        //     print_r($th->getFile());
        //     print_r($th->getLine());
        //     print_r($th->getMessage());
        //     print_r($ex->getTrace());
        // }

        finally
        {
            Response::json($response);
        }
    }

    #[Route(verb: 'POST', uri: '/api/app/upsell/send-pin')]
    public function upsellSendPin(Request $request)
    {
        header("Content-Type: application/json");

        try
        {
            $body = $request->json();
            $email = $body->email ?? '';
            $email = strtolower(base64_decode($email));
            if (!$email) throw new CustomerExceptions\EmptyEmailException;

            $customer = Customer::where('email', $email)->first();
            if (!$customer) throw new CustomerExceptions\CustomerNotFoundException;

            if (strtotime($customer->upsell_pin_sent_at) > strtotime(today()))
                throw new CustomerExceptions\TimeoutException("Você precisa aguardar até " . date_br($customer->upsell_pin_sent_at) . " para realizar uma nova tentativa.");

            $n1 = strval(rand(0, 9));
            $n2 = strval(rand(0, 9));
            $n3 = strval(rand(0, 9));
            $n4 = strval(rand(0, 9));
            $n5 = strval(rand(0, 9));
            $customer->upsell_pin = "$n1$n2$n3$n4$n5";
            $customer->upsell_pin_at = today();
            $customer->upsell_pin_sent_at = date("Y-m-d H:i:s", strtotime(today() . " + 2 minutes"));
            $customer->save();

            /**
             * Envia e-mail
             */
            $email_vars = compact('customer');

            Email::to($customer->email)
                ->title('RocketPays')
                ->subject("Seu PIN")
                ->body(Email::view('pinCustomer', $email_vars))
                ->send();

            // --

            $response = ["status" => "success"];
        }

        catch (CustomerExceptions\EmptyEmailException)
        {
            $response = ["status" => "error", "code" => 1, "message" => "O e-mail não pode estar em branco."];
        }

        catch (CustomerExceptions\CustomerNotFoundException)
        {
            $response = ["status" => "error", "code" => 2, "message" => "Cliente não encontrado."];
        }

        catch (CustomerExceptions\TimeoutException $ex)
        {
            $response = ["status" => "error", "code" => 3, "message" => $ex->getMessage()];
        }


        finally
        {
            Response::json($response);
        }
    }

    #[Route(verb: 'POST', uri: '/api/app/upsell/verify-email')]
    public function upsellVerifyEmail(Request $request)
    {
        header("Content-Type: application/json");

        try
        {
            $body = $request->json();
            $req_pin = $body->pin;
            $email = $body->email;

            $customer = Customer::where('email', $email)->first();
            if (empty($customer)) throw new CustomerExceptions\CustomerNotFoundException;

            $cus_pin = $customer->upsell_pin ?? '';

            if (strtotime($customer->upsell_pin_next_at) > strtotime(today()))
                throw new CustomerExceptions\TimeoutException("Você precisa aguardar até " . date_br($customer->upsell_pin_next_at) . " para realizar uma nova tentativa.");

            // TODO: se vc errar o pin, ele apaga, dai precisa gerar outro em: 1 minuto, 3 minutos, 5 minutos, 10 minutos, 20 minutos, 30 minutos, 1h

            $customer->upsell_pin = null; // apaga o pin quando erra
            $customer->upsell_pin_next_at = date("Y-m-d H:i:s", strtotime(today() . " + 5 minutes")); // proxima tentativa esta disponivel em
            $customer->upsell_pin_at = date("Y-m-d H:i:s", strtotime(today() . " + 30 minutes")); // expiracao do PIN
            $customer->save();

            // checar se o token recebido no email eh valido
            if (strlen($req_pin) <> 5 || strlen($cus_pin) <> 5 || $req_pin <> $cus_pin)
                throw new CustomerExceptions\WrongPinException;

            $customer->upsell_token = ghash();
            $customer->upsell_token_at = date("Y-m-d H:i:s", strtotime(today() . " + 1 hour"));
            $customer->save();

            $phone = $customer->phone;
            if (substr($phone, 0, 2) == 5) $phone = substr($phone, 2);

            $response = [
                "status" => "success",
                "data" => [
                    "token" => $customer->upsell_token,
                    "name" => $customer->name,
                    "phone" => $phone,
                    "doc" => $customer->doc,
                    "zipcode" => $customer->address_zipcode,
                    "street" => $customer->address_street,
                    "number" => $customer->address_number,
                    "neighborhood" => $customer->address_district,
                    "complement" => $customer->address_complement,
                    "city" => $customer->address_city,
                    "state" => $customer->address_state,
                    "gender" => $customer->gender
                ]
            ];
        }

        catch (CustomerExceptions\CustomerNotFoundException)
        {
            $response = ["status" => "success", "message" => "Cliente não encontrado."];
        }

        catch (CustomerExceptions\WrongPinException)
        {
            $response = ["status" => "success", "message" => "PIN inválido."];
        }

        catch (CustomerExceptions\TimeoutException $ex)
        {
            $response = ["status" => "error", "message" => $ex->getMessage()];
        }

        finally
        {
            Response::json($response);
        }
    }
}
