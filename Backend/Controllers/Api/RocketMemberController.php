<?php

namespace Backend\Controllers\Api;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use \Illuminate\Database\Eloquent\ModelNotFoundException;
# use Backend\Exception\Api\RocketMember\NotAuthorizedException;
use Backend\Models\Product;
use Backend\Models\Customer;
use Backend\Models\Order;
use Backend\Models\User;

class RocketMemberController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
    }

    private function headers()
    {
        header("Content-Type: application/json");
    }

    private function middleware()
    {
        $authorization = substr(getallheaders()['Authorization'] ?? '', 7);
        $body = json_decode(file_get_contents("php://input"));

        if ($authorization <> '95ca9a6b8745856328a67e3452d820e7') die(Response::json([
            "status" => "error", "code" => "NOT_AUTHORIZED", "message" => "Credênciais inválidas."
        ]));
    }

    public function getProduct(Request $request, $id)
    {
        $this->headers();
        $this->middleware();
        $response = null;
        
        try
        {
            $product = Product::findOrFail($id);
            $response = ["status" => "success", "code" => "OK", "message" => "Produto encontrado.", "data" => $product];
        }

        catch (ModelNotFoundException $ex)
        {
            $response = ["status" => "error", "code" => "PRODUCT_NOT_FOUND", "message" => "Produto não encontrado."];
        }

        finally
        {
            Response::json($response);
        }
    }

    public function getCustomer(Request $request, $id)
    {
        $this->headers();
        $this->middleware();
        $response = null;
        
        try
        {
            $customer = Customer::findOrFail($id);
            unset($customer->access_token);
            unset($customer->password);
            $response = ["status" => "success", "code" => "OK", "message" => "Cliente encontrado.", "data" => $customer];
        }

        catch (ModelNotFoundException $ex)
        {
            $response = ["status" => "error", "code" => "CUSTOMER_NOT_FOUND", "message" => "Cliente não encontrado."];
        }

        finally
        {
            Response::json($response);
        }
    }

    public function getUser(Request $request, $id)
    {
        $this->headers();
        $this->middleware();
        $response = null;
        
        try
        {
            $user = User::findOrFail($id);
            unset($user->jwt);
            unset($user->rocketpanel_access_token);
            unset($user->access_token);
            unset($user->password);
            $response = ["status" => "success", "code" => "OK", "message" => "Usuário encontrado.", "data" => $user];
        }

        catch (ModelNotFoundException $ex)
        {
            $response = ["status" => "error", "code" => "USER_NOT_FOUND", "message" => "Usuário não encontrado."];
        }

        finally
        {
            Response::json($response);
        }
    }

    public function getOrder(Request $request, $id)
    {
        $this->headers();
        $this->middleware();
        $response = null;
        
        try
        {
            $order = Order::findOrFail($id);
            $response = ["status" => "success", "code" => "OK", "message" => "Pedido encontrado.", "data" => $order];
        }

        catch (ModelNotFoundException $ex)
        {
            $response = ["status" => "error", "code" => "PRODUCT_NOT_FOUND", "message" => "Pedido não encontrado."];
        }

        finally
        {
            Response::json($response);
        }
    }
}