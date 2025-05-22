<?php

namespace Backend\Controllers\Subdomains\Purchase\Login;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Http\Link;
use Backend\Template\View;
use Backend\Controllers\Browser\NotFoundController;
use Backend\Exceptions\Auth\WrongPasswordException;
use Backend\Models\Customer;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Login';
        $this->context = 'public';
        $this->indexFile = 'frontend/view/subdomains/purchase/login/indexView.php';
        $this->subdomain = 'purchase';
        $this->domain = get_subdomain_serialized($this->subdomain);
        // $this->user = user();
    }

    public function index(Request $request)
    {
        // $sku = substr($request->uri(), 1);
        $title = $this->title;
        $context = $this->context;
        // $user = $this->user;

        // $product = Product::where('sku', $sku)->first();
        // if (empty($product)) throw new ProductNotFoundException;
        View::render($this->indexFile, compact('title', 'context'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        // $user = $this->user;

        $params = $request->pageParams();
        // $sku = $params->sku;

        // $product = Product::where('sku', $sku)->first();
        // if (empty($product)) throw new ProductNotFoundException;
        View::response($this->indexFile, compact('title', 'context'));
    }

    public function auth(Request $request)
    {
        header("Content-Type: application/json");

        $body = $request->json();
        $login = $body->login ?? '';
        $password = $body->password ?? '';

        $response = [];

        try
        {
            if (empty($login) || empty($password))
                throw new WrongPasswordException("Login ou senha em branco.");

            $customer = Customer::where('email', $login)->where('password', hash_make($password))->first();
            if (empty($customer)) throw new WrongPasswordException('Login incorreto.');

            $customer->access_token = ghash();
            $customer->save();

            c_authenticate($customer->access_token);

            // apagar ao deslogar ^

            $response = ["status" => "success"];
        }

        catch (WrongPasswordException $th)
        {
            $response = ["status" => "error", "message" => $th->getMessage()];
        }

        // setar session no dominio principal
        // $cag = c_authenticate_g($customer->access_token);
        // $response = ["status" => "error"];

        return Response::json($response);
    }

    public function logout(Request $request)
    {
        logout();
        $this->index($request);
        Link::changeUrl($this->domain, '/login');
    }

    public function token(Request $request, $hash)
    {
        c_logout();

        $customer = Customer::where('one_time_access_token', $hash)->first();

        if (empty($customer) || !($access_token = $customer?->access_token) || !($one_time_access_token = $customer->one_time_access_token))
            Response::redirect($this->domain);

        // faz a autenticacao do cliente
        // $customer->access_token = ghash();
        c_authenticate($customer->access_token);

        // novo token de acesso para o cliente nao poder mais fazer login
        // com o token anterior
        // $customer->one_time_access_token = ghash();
        // $customer->save();

        Response::redirect($this->domain);
    }
}