<?php

namespace Backend\Controllers\Api;

use Backend\App;
use Backend\Models\User;
use Backend\Http\Request;
use Backend\Http\Response;

class RocketPanelController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
    }

    /**
     * Autenticacao das requisicoes
     * 
     * @return Bool
     */
    protected function authMiddleware(Request $request) : Bool
    {
        $authorization = $request->header('Authorization');
        $authorization = str_replace("Basic ", "", $authorization);
        $aux = explode(":", base64_decode($authorization));
        $user = $aux[0] ?? '';
        $password = $aux[1] ?? '';

        if ($user <> env('PLATFORMS_AUTOLOGIN_USER') || $password <> env('PLATFORMS_AUTOLOGIN_PASS')) 
            return false;

        return true;
    }

    public function register(Request $request)
    {
       if (!$this->authMiddleware($request)) return Response::json(['status' => 'error', 'message' => 'Access denied.']);

       $body = $request->json();

       $email = strtolower($body->user->email ?? '');
       $name = $body->user->name ?? '';
       $password = $body->user->password ?? '';

       if (empty($email)) return Response::json(['status' => 'error', 'message' => 'Empty email.']);

       $user = User::where('email', $email)->first();
       $user_by_rp = User::where('email', $email)->where('created_by_rocket_panel', 1)->first();
       $was_created_by_RocketPanel = !empty($user_by_rp);

       $rocketpanel_access_token = $access_token = ghash();
       $password = hash_make($password);

       $user_data = compact(
           'email',
           'name',
           'password',
           'access_token',
           'rocketpanel_access_token'
       );

       $user_data['created_by_rocket_panel'] = 1;
        
       $registered_now = false;

       // se o e-mail nao existe
       if (empty($user))
       {
           // criar uma conta nova utilizando o e-mail que veio da requisicao
           // echo "se o e-mail nao existe $email";

           $user = User::create($user_data);
           $registered_now = true;

           // vincular ao plano
       }

       // o e-mail existe e foi criado via RocketPanel
       else if (!empty($user) && $was_created_by_RocketPanel)
       {
           // echo "O e-mail existe e foi criado via RocketPanel";

        //    $user_data['email'] = "user_" . sha1(uniqid()) . "@".site_host();
        //    $user_data['rocketpanel_access_token'] = $access_token;
        //    $user = User::create($user_data); // LEMBRAR: aqui deveria ser atualizar o token e nao criar usuario
            $user = $user->update([
                "rocketpanel_access_token" => $access_token,
                "access_token" => $access_token,
            ]);
       }

       // O e-mail existe mas nao foi criado via RocketPanel
       else if (!empty($user) && !$was_created_by_RocketPanel)
       {
           // criar uma conta nova com um e-mail aleatorio, ex.: user_0e0e0e000eff0f0f0c0c0c00c011f@email.com
           // echo "O e-mail existe mas nao foi criado via RocketPanel";
           
           $user_data['email'] = "user_" . sha1(uniqid()) . "@".site_host();
           $user_data['rocketpanel_access_token'] = $access_token;
           $user = User::create($user_data);
           $access_token = $user->access_token ?? '';
           $registered_now = true;

           // vincular ao plano
       }

       if (empty($user)) return Response::json(['status' => 'error', 'message' => 'User not found.']);

       // retornar o token de acesso
        return Response::json([
           'status' => 'success',
           'email' => $user->email,
           'registered_now' => $registered_now,
           'access_token' => $access_token
       ]);
    }

    /**
     * Acao de registrar usuario vindo do checkout de planos
     *
     * @param Request $request
     */
    public function checkout_register(Request $request)
    {
        if (!$this->authMiddleware($request)) return Response::json(['status' => 'error', 'message' => 'Access denied.']);
 
        $body = $request->json();
        $access_token = base64_encode(uniqid());
        $email = strtolower($body->user->email ?? '');
        $name = $body->user->name ?? '';
        $password = $body->user->password ?? '';
 
        if (empty($email)) return Response::json(['status' => 'error', 'message' => 'Empty email.']);
 
        $user = User::where('email', $email)->first();
        $user_by_rp = User::where('email', $email)->where('created_by_rocket_panel', 1)->first();
        $was_created_by_RocketPanel = !empty($user_by_rp);
 
        $rocketpanel_access_token = $access_token = ghash();
 
        $user_data = compact(
            'email',
            'name',
            'access_token', // token de acesso do proprio site
            'rocketpanel_access_token' // token de acesso compartilhado com a RocketPanel
        );
 
        $user_data['created_by_rocket_panel'] = 1;
        $registered_now = false;

        if (!empty($user)) return Response::json(['status' => 'error', 'message' => 'User already exists.']);

        $user = User::create($user_data);
        $user->password = hash_make($password);
        $registered_now = true;
        $user->user_id_rocket_panel = $body->user->user_id_rocket_panel ?? '';
        $user->sku = strtoupper(uniqid());
        $user->save();

        // if (empty($user))
        // {
        // }
        // else
        // {
        //     // foi criado via RocketPanel?
        //     if ($was_created_by_RocketPanel)
        //     {
        //         // apenas mudar o token de acesso
        //         // ------------------------------

        //         $user->rocketpanel_access_token = $access_token;
        //         $user->access_token = $access_token;
        //         $user->save();
        //     }

        //     // nao foi criado via RocketPanel
        //     else if (!$was_created_by_RocketPanel)
        //     {
        //         // criar novo usuario de email aleatorio
        //         // -------------------------------------

        //         // criar uma conta nova com um e-mail aleatorio, ex.: user_0e0e0e000eff0f0f0c0c0c00c011f@email.com
        //         // echo "O e-mail existe mas nao foi criado via RocketPanel";
                
        //         $user_data['email'] = "user_".sha1(uniqid())."@".site_host();
        //         $user_data['rocketpanel_access_token'] = $access_token;
        //         $user = User::create($user_data);
        //         $access_token = $user->access_token ?? '';
        //         $registered_now = true;
        //     }
        // }

        return Response::json([
            "status" => "success",
            "email" => $user->email, // o email retornado eh o que foi criado como usuario aqui neste site
            "registered_now" => $registered_now,
            "access_token" => $access_token
        ]);
    }

    /**
     * Acao de login via RocketPanel
     */
    public function login(Request $request)
    {
        if (!$this->authMiddleware($request)) return Response::json(['status' => 'error', 'message' => 'Access denied.']);

        $body = $request->json();
        $access_token = $body->access_token;
        $access_token_e = base64_encode(cryptoJsAesEncrypt(env('PLATFORMS_AUTOLOGIN_PASS'), $access_token));
        $user = User::where('rocketpanel_access_token', $access_token)->first();
        if (empty($user)) return Response::json(['status' => 'error', 'message' => 'User not found.']);
        
        return Response::json(['status' => 'success', 'url' => site_url().'/api/rocketpanel/auth?access_token='.$access_token_e]);
    }
    
    public function auth(Request $request)
    {
        $access_token = $request->query('access_token');
        $access_token = cryptoJsAesDecrypt(env('PLATFORMS_AUTOLOGIN_PASS'), base64_decode($access_token));
        $user = User::where('rocketpanel_access_token', $access_token)->first();
        if (!empty($user))
        {
            if ($user->status == 'expired')
            {
                header("location: ".env('EXPIRED_USER_REDIRECT'));
            }

            else
            {
                $user->access_token = $access_token;
                $user->save();
                authenticate($access_token);
                $link = site_url().'/dashboard';
                header("location: $link");
            }
        }
    }

    public function cancel(Request $request)
    {
        $body = $request->json();
        $user = User::where('rocketpanel_access_token', $body->access_token)->first();
        if (!empty($user))
        {
            $user->status = 'expired';
            $user->save();
        }
    }

    public function activate(Request $request)
    {
        $body = $request->json();
        $user = User::where('rocketpanel_access_token', $body->access_token)->first();
        if (!empty($user))
        {
            $user->status = 'active';
            $user->expires_at = $body->expires_at;
            $user->save();
        }
    }

    public function checkout_update(Request $request)
    {
        $body = $request->json();
        $user = User::where('rocketpanel_access_token', $body->access_token)->first();
        if (!empty($user))
        {
            if ($body->expires_at ?? false) $user->expires_at = $body->expires_at;
            if ($body->status ?? false) $user->status = $body->status;
            $user->save();
        }
    }

    public function cron_user_expired()
    {
        $users = User::where('expires_at', '<', date("Y-m-d H:i:s"))->where('status', '!=', 'expired')->get();
        foreach ($users as $user)
        {
            $user->status = 'expired';
            $user->save();
        }
    }
}