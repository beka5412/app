<?php

namespace Backend\Controllers\User\Customer;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Product;
use Backend\Models\Customer;
use Backend\Exceptions\Customer\EmptyNameException;
use Backend\Exceptions\Customer\EmptyEmailException;
use Backend\Exceptions\Customer\CustomerNotFoundException;

class EditController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Editar cliente';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/customers/editView.php';
        $this->user = user();
    }

    public function index(Request $request, $id)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $customer = Customer::where('id', $id)->where('user_id', $user->id)->first();

        View::render($this->indexFile, compact('title', 'context', 'user', 'customer'));
    }

    public function element(Request $request)
    {
        $body = $request->pageParams();
        $id = $body?->id;
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $customer = Customer::where('id', $id)->where('user_id', $user->id)->first();

        View::response($this->indexFile, compact('title', 'context', 'user', 'customer'));
    }

    public function update(Request $request, $id)
    {
        $user = $this->user;
        $response = [];
        $body = $request->json();
        $name = $body->name;
        $email = $body->email;

        try
        {
            $customer = Customer::where('id', $id)->where('user_id', $user->id)->first();

            if (empty($customer)) throw new CustomerNotFoundException;
            if (!$name) throw new EmptyNameException;
            if (!$email) throw new EmptyEmailException;

            $customer->name = $name;
            $customer->email = $email;
            $customer->save();

            $response = ["status" => "success", "message" => "Cliente atualizado com sucesso."];
        }

        catch(CustomerNotFoundException $ex)
        {
            $response = ["status" => "error", "message" => "Cliente não encontrado."];
        }

        catch(EmptyNameException $ex)
        {
            $response = ["status" => "error", "message" => "O nome do cliente não pode estar em branco."];
        }

        catch(EmptyEmailException $ex)
        {
            $response = ["status" => "error", "message" => "O e-mail do cliente não pode estar em branco."];
        }

        finally
        {
            Response::json($response);
        }
    }
}