<?php

namespace Backend\Controllers\Subdomains\Purchase\ResetPassword;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Http\Link;
use Backend\Template\View;
use Backend\Controllers\Browser\NotFoundController;
use Backend\Exceptions\Auth\WrongPasswordException;
use Backend\Notifiers\Email\Mailer as Email;
use Backend\Models\Customer;
use Backend\Models\ResetPasswordRequest;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Login';
        $this->context = 'public';
        $this->indexFile = 'frontend/view/subdomains/purchase/reset_password/indexView.php';
        $this->subdomain = 'purchase';
        $this->domain = get_subdomain_serialized($this->subdomain);
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

    public function submit(Request $request)
    {
        $body = $request->json();
        $email = strtolower($body->email);
      
        $customer = Customer::where('email', $email)->first();
        
        if (!empty($customer))
        {
            $token = ghash();

            $last_reset_password = ResetPasswordRequest::where('customer_id', $customer->id)->orderBy('id', 'DESC')->first();

            if (!empty($last_reset_password))
            {
                $last_created_at = strtotime($last_reset_password->created_at);
                if ($last_created_at + 60 > time()) return Response::json(['status' => 'error', 'message' => 'Aguarde pelo menos 1 minuto para solicitar novamente.']);
            }


            $time_string = '3 dias';
            $time = '3 days';


            ResetPasswordRequest::where('customer_id', $customer->id)->update(['is_available' => 0]);

            $reset_password = new ResetPasswordRequest;
            $reset_password->customer_id = $customer->id;
            $reset_password->token = $token;
            $reset_password->is_available = 1;
            $reset_password->expires_at = date('Y-m-d H:i:s', strtotime(today().' + '.$time));
            $reset_password->save();

            $btn_url = get_subdomain_serialized('purchase')."/reset/password/".urlencode($token);

            Email::to($email)
                ->title('RocketPays')
                ->subject('Redefinição de senha do cliente')
                ->body(Email::view('resetPasswordCustomer', compact('btn_url', 'token', 'time_string')))
                ->send();
        }

        return Response::json(['status' => 'success', 'message' => 'Um link de redefinição da senha foi enviado para o seu e-mail.']);
    }
}