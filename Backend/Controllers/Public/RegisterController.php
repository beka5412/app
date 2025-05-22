<?php

namespace Backend\Controllers\Public;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Models\Balance;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Exceptions\RocketPays\CreateAccountException;

class RegisterController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Cadastro';
        $this->context = 'form';
        $this->indexFile = 'frontend/view/public/registerView.php';
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        View::render($this->indexFile, compact('title', 'context'));
    }

    // criar rota automaticamente com esse atributo + doc
    public function old_register(Request $request)
    {
        $endpoint = env('PANEL_URL');
        $return = [];
        try
        {
            $body = $request->json();
            $name = $body->name;
            $email = $body->email;
            $password = $body->password;
            $doc = $body->doc;
            $phone = $body->phone;
            $birthdate = $body->birthdate;

            $http = new \GuzzleHttp\Client(["base_uri" => $endpoint]);
            $response = $http->post('/api/register', [
                "json" => compact(
                    "name",
                    "email",
                    "password",
                    "doc",
                    "phone",
                    "birthdate"
                )
                // ,
                // "headers" => [
                //     "Content-Type" => "application/json"
                // ]
            ]);
    
            $status_code = $response->getStatusCode();
            $body = $response->getBody();


            if ($status_code == 200)
            {
                $json = json_decode($body);
                // $user_id = $json->data->user_id;
                // $user_id_rocket_panel = $json->data->user_id_rocket_panel;
                // if ($user_id)
                // {
                //     auth()->login($user_id);
                // }
                // echo $body;

                // logar no painel

                $access_token = $json->data->access_token ?? '';
                if (empty($access_token)) throw new CreateAccountException;
                
                // redirecionar para a rocketpays
                $url = "$endpoint/autologin";
                $token = bin2hex($access_token); // cipher AES-256
                $return = [
                    "status" => "success",
                    "url" => $url,
                    "token" => $token,
                    "full" => bin2hex("$url/$token"),
                    "redirect" => "$url/$token"
                ];
            }
        }

        catch (\GuzzleHttp\Exception\ClientException|CreateAccountException)
        {
            $return = [
                "status" => "error",
                "message" => "Erro ao criar conta."
            ];
        }

        finally
        {
            return Response::json($return);
        }
    }

    public function register(Request $request)
    {
        $body = $request->json();
        $name = sanitize($body->name);
        $email = strtolower(sanitize($body->email));
        $password = sanitize($body->password);

        $user = User::where('email', $email)->first();
        if ($user)
        {
            return Response::json([
                'status' => 'error',
                'message' => 'Este e-mail já foi registrado.'
            ]);
        }

        $user = new User;
        $user->email = $email;
        $user->name = $name;
        $user->password = hash_make($password);
        $user->sku = strtoupper(uniqid());
        $user->access_token = ghash();
        $user->user_id_rocket_panel = ghash();
        $user->save();

        $balance = new Balance;
        $balance->user_id = $user->id;
        $balance->save();

        authenticate($user->access_token);

        $url = site_url().'/dashboard';

        // ----------------------------------------

        $token = base64_encode(env('PLATFORMS_AUTOLOGIN_USER').":".env('PLATFORMS_AUTOLOGIN_PASS'));

        $headers = [
            "Authorization: Basic $token",
            "Content-Type: application/json",
            "User-Agent: Migraz/1.0"
        ];

        $payload = [
            "user" => [
                "id" => $user->id,
                "name" => $user->name,
                "email" => $email,
                "password" => $password,
                "user_id_rocket_panel" => $user->user_id_rocket_panel
            ],
            "meta" => null,
            "subscription" => null,
            "token" => $token,
            "plan_id" => 1,
            "expires_at" => date("Y-m-d H:i:s", strtotime(today() . " + 100 years"))
        ];

        $body = json_encode($payload);

        $curl = curl_init("https://member.migraz.com/rocketpanel/checkout/register");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($curl, CURLOPT_TIMEOUT, 40);
        $response = curl_exec($curl);
        $curl_errno = curl_errno($curl);
        $curl_error = curl_error($curl);
        curl_close($curl);
        // ----------------------------------------

        $json = json_decode($response);

        if ($json->access_token ?? false)
        {
            $user->members_access_token = $json->access_token;
            $user->save();
        }

        $return = [
            "status" => "success",
            "url" => $url,
            "token" => "",
            "full" => "",
            "redirect" => $url
        ];

        return Response::json($return);
    }
}