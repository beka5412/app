<?php

namespace Backend\Controllers\Public;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Exceptions\Auth\WrongPasswordException;
use Backend\Models\User;

class LoginController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Entrar';
        $this->context = 'form';
        $this->indexFile = 'frontend/view/public/loginView.php';
    }

    // public function index(Request $request)
    // {
    //     $title = $this->title;
    //     $context = $this->context;
    //     View::render($this->indexFile, compact('title', 'context'));
    // }

    public function index(Request $request)
    {
        $url = env('REDIRECT_LOGIN');

        if ($url)
            header("Location: $url");

        else 
        {
            $title = $this->title;
            $context = $this->context;
            View::render($this->indexFile, compact('title', 'context'));
        }
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        View::response($this->indexFile, compact('title', 'context'));
    }

    public function auth(Request $request)
    {   
        header("Content-Type: application/json");

        $body = $request->json();
        $login = $body->login ?? '';
        $password = $body->password ?? '';

        try
        {
            if (empty($login) || empty($password))
                throw new WrongPasswordException("Login ou senha em branco.");

            $user = User::where('email', $login)->where('password', hash_make($password))->first();
            if (empty($user)) throw new WrongPasswordException('Login incorreto.');

            // $user->access_token = ghash();
            // $user->save();

            $_SESSION[env('USER_AUTH_KEY')] = $user->access_token;
            // apagar ao deslogar ^
        }

        catch (WrongPasswordException $th)
        {
            return Response::json(["status" => "error", "message" => $th->getMessage()]);
        }

        return Response::json(["status" => "success"]);

    }
}
