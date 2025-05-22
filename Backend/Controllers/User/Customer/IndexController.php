<?php

namespace Backend\Controllers\User\Customer;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Customer;
use Backend\Exceptions\Customer\CustomerNotFoundException;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Customers';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/customers/indexView.php';
        $this->user = user();
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        
        $page = $request->query('page') ?: 1;
        $per_page = 10;
        $url = get_current_route();

        $customers = Customer::where('user_id', $user->id)->orderBy('id', 'DESC')
            ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);

        $info = $customers;

        View::render($this->indexFile, compact('title', 'context', 'user', 'customers', 'info', 'url'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        $page = $request->query('page') ?: 1;
        $per_page = 10;
        $url = site_url().'/customers';

        $customers = Customer::where('user_id', $user->id)->orderBy('id', 'DESC')
            ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);

        $info = $customers;

        View::response($this->indexFile, compact('title', 'context', 'user', 'customers', 'info', 'url'));
    }

    public function full(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $customers = Customer::where('user_id', $user->id)->orderBy('id', 'DESC')->paginate(10);
        View::render($this->indexFile, compact('title', 'context', 'user', 'customers'), ['no_js' => true]);
    }

    public function destroy(Request $request, $id)
    {
        $user = $this->user;
        $response = [];

        try
        {
            $customer = Customer::where('id', $id)->where('user_id', $user->id)->first();
            if (empty($customer)) throw new CustomerNotFoundException;

            $customer->delete();

            $response = ["status" => "success", "message" => "Cliente deletado com sucesso."];
        }

        catch(CustomerNotFoundException $ex)
        {
            $response = ["status" => "error", "message" => "Cliente não encontrado."];
        }

        finally
        {
            Response::json($response);
        }
    }

    public function new(Request $request)
    {
        $user = $this->user;

        $customer = new Customer;
        $customer->user_id = $user->id;
        $customer->name = "Sem Nome #".time();
        $customer->status = 'active';
        $customer->save();

        Response::json(["message" => "Cliente criado com sucesso.", "id" => $customer->id]);
    }
}