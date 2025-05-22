<?php

namespace Backend\Controllers\Public;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Http\Link;
use Backend\Template\View;
use Backend\Models\Administrator;
use Backend\Models\Product;
use Backend\Controllers\Browser\NotFoundController;
use Backend\Exceptions\Auth\WrongPasswordException;

class AdminLoginController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Login';
        $this->context = 'form';
        $this->indexFile = 'frontend/view/public/adminLoginView.php';
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;

        View::render($this->indexFile, compact('title', 'context'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;

        $params = $request->pageParams();

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

            $admin = Administrator::where('email', $login)->where('password', hash_make($password))->first();
            if (empty($admin)) throw new WrongPasswordException('Login incorreto.'); 

            $admin->access_token = ghash();
            $admin->save();

            a_authenticate($admin->access_token);
            // apagar ao deslogar ^

            $response = ["status" => "success"];
        }

        catch (WrongPasswordException $th)
        {
            $response = ["status" => "error", "message" => $th->getMessage()];
        }

        return Response::json($response);
    }

    public function logout(Request $request)
    {
        unset($_SESSION[env('ADMIN_AUTH_KEY')]);
        $this->index($request);
        Link::changeUrl(site_url(), '/admin/login');
    }
}