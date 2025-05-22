<?php

namespace Backend\Controllers\Subdomains\Purchase\ResetPassword\CreateNewPassword;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Exceptions\Customer\ResetPassword\CreateNewPassword\InvalidPasswordException;
use Backend\Exceptions\Customer\ResetPassword\CreateNewPassword\TokenNotFoundException;
use Backend\Exceptions\Customer\ResetPassword\CreateNewPassword\EmptyTokenException;
use Backend\Exceptions\Customer\ResetPassword\CreateNewPassword\CustomerNotFoundException;
use Backend\Models\ResetPasswordRequest;
use Backend\Models\Customer;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Login';
        $this->context = 'public';
        $this->indexFile = 'frontend/view/subdomains/purchase/reset_password/create_new_password/indexView.php';
        $this->subdomain = 'purchase';
        $this->domain = get_subdomain_serialized($this->subdomain);
    }

    public function index(Request $request, $token)
    {
        $title = $this->title;
        $context = $this->context;

        View::render($this->indexFile, compact('title', 'context', 'token'));
    }

    /**
     * Ação do botão "salvar nova senha"
     *
     * @param Request $request
     * @throws InvalidPasswordException
     * @throws TokenNotFoundException
     * @throws CustomerNotFoundException
     * @return void
     */
    public function save(Request $request) : void
    {
        $body = $request->json();
        
        $password = $body->password ?? '';
        $confirm_password = $body->confirm_password ?? '';
        $token = $body->token ?? '';

        $response = ["status" => "pending"];

        try
        {
            if (!cmp_both_valid($password, '==', $confirm_password))
                throw new InvalidPasswordException;

            if (!$token) throw new EmptyTokenException;

            $reset_password_request = ResetPasswordRequest::where('token', $token)->where('is_available', 1)->first();
            if (empty($reset_password_request)) throw new TokenNotFoundException;

            $customer_id = $reset_password_request->customer_id;
            $customer = Customer::find($customer_id);
            if (empty($customer)) throw new CustomerNotFoundException;
            $customer->password = hash_make($password);
            $customer->save();

            
            // invalida todas os tokens de redefinicao deste cliente
            $reset_password_request->is_available = 0;
            $reset_password_request->used_at = today();
            $reset_password_request->save();    

            ResetPasswordRequest::where('customer_id', $customer->id)->update(['is_available' => 0]);

            // faz login
            c_authenticate($customer->access_token);


            $response = ["status" => "success", "message" => __("Password changed successfully.")];
        }

        catch (InvalidPasswordException)
        {
            $response = ["status" => "error", "message" => __("Passwords do not match.")];
        }

        // catch (TokenNotFoundException)
        // {
        //     $response = ["status" => "error", "message" => __("a")];
        // }

        // catch (CustomerNotFoundException)
        // {
        //     $response = ["status" => "error", "message" => __("b")];
        // }

        catch (TokenNotFoundException|CustomerNotFoundException)
        {
            $response = ["status" => "error", "message" => __("This link has expired! Please try requesting a password reset again.")];
        }

        finally
        {
            Response::json($response);
        }
    }
}