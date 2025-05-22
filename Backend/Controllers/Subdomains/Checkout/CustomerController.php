<?php

namespace Backend\Controllers\Subdomains\Checkout;

use Backend\App;
// use Backend\Exceptions\Coupon\CouponNotFoundException;
use Backend\Enums\Lib\Session;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Exceptions\Customer as CustomerExceptions;
use Backend\Exceptions\Pagarme as PagarmeExceptions;
use Backend\Exceptions\Ipag as IpagExceptions;
use Backend\Services\
{
    IPag\IPag,
    PagarMe\PagarMe
};
// use Backend\Models\User;
use Backend\Models\Customer;
use Backend\Models\PagarmeCustomer;
use Backend\Models\IpagCustomer;

class CustomerController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Checkout';
        $this->context = 'public';
        $this->user = user();
        $this->subdomain = 'checkout';
        $this->domain = get_subdomain_serialized($this->subdomain);
    }

    public function main(Request $request)
    {
        // $title = $this->title;
        // $context = $this->context;
        // $user = $this->user;

        // $body = $request->json();
        // $coupon = $body->coupon ?? '';

        // $response = [];

        // try 
        // {
        //     $coupon = Coupon::where('code', $coupon)
        //     // ->where('product_id', $product_id)
        //     ->first();
    
        //     if (empty($coupon)) throw new CouponNotFoundException;

        //     put_session(Session::CHECKOUT, 'coupon_applied_id', $coupon->id);
    
        //     $response = [
        //         "status" => "success",
        //         "message" => "Cupom aplicado com sucesso.",
        //         "data" => [
        //             "type" => $coupon?->type,
        //             "discount" => $coupon?->discount
        //         ]
        //     ];
        // }

        // catch (CouponNotFoundException $ex)
        // {
        //     $response = ["status" => "error", "message" => "Cupom inválido."];
        // }

        // finally 
        // {
        //     Response::json($response);
        // }
    }

    /**
     * Find lastest card
     *
     * @param Request $request
     * @throws CustomerExceptions\CustomerNotFoundException
     * @throws PagarmeExceptions\CustomerNotFoundException
     * @throws PagarmeExceptions\EmptyCustomerIDException
     * @throws PagarmeExceptions\EmptyCardsException
     * @return void
     */
    public function latestCreditCard(Request $request)
    {
        $pagarme = new PagarMe;
        $ipag = new Ipag;
        $gateway = get_setting('gateway');

        try
        {
            $token = $request->header('Data');
            $customer = Customer::where('upsell_token', $token)->first();
            
            if (empty($customer)) throw new CustomerExceptions\CustomerNotFoundException;

            if ($gateway == 'pagarme')
            {
                $pagarme_customer = PagarmeCustomer::where('customer_id', $customer->id)->where('pm_customer_id', '<>', null)->orderBy('id', 'DESC')->first();
                if (empty($pagarme_customer)) throw new PagarmeExceptions\CustomerNotFoundException;

                if (empty($pagarme_customer->pm_customer_id)) throw new PagarmeExceptions\EmptyCustomerIDException;

                $result = $pagarme->getCards($pagarme_customer->pm_customer_id);
                $json = json_decode($result);
                $cards = $json->data ?? [];

                if (empty($cards)) throw new PagarmeExceptions\EmptyCardsException;
            }

            else if ($gateway == 'ipag')
            {
                $card_tokens = IpagCustomer::where('customer_id', $customer->id)->where('card_token', '<>', null)->orderBy('id', 'DESC')->get();
                if (empty($card_tokens)) throw new IpagExceptions\CustomerNotFoundException;
                
                foreach (array_map( fn($card) => json_decode($card), $ipag->getCards(array_map(fn($row) => $row->card_token, [...$card_tokens])) ) as $card)
                {
                    $cards[] = (object) [
                        "id" => $card->token,
                        "first_six_digits" => $card->attributes->card->bin,
                        "last_four_digits" => $card->attributes->card->last4,
                        "brand" => ucwords($card->attributes->card->brand),
                        "holder_name" => $card->attributes->card->holder,
                        "holder_document" => $card->attributes->holder->cpf,
                        "exp_month" => '0',
                        "exp_year" => '0000',
                        "status" => $card->attributes->card->is_active ? 'active' : '',
                        "type" => "credit",
                        "created_at" => date("Y-m-d\TH:i:s\Z", strtotime($card->attributes->created_at)),
                        "updated_at" => date("Y-m-d\TH:i:s\Z", strtotime($card->attributes->updated_at))
                    ];
                }
                
                if (empty($cards)) throw new IpagExceptions\EmptyCardsException;        
            }

            

            
            // "id": "card_P6KOVafygTd8zNAV",
            // "first_six_digits": "472953",
            // "last_four_digits": "3710",
            // "brand": "Visa",
            // "holder_name": "Rocketleads Digital",
            // "holder_document": "34378821000188",
            // "exp_month": 5,
            // "exp_year": 2028,
            // "status": "active",
            // "type": "credit",
            // "created_at": "2023-05-16T15:28:23Z",
            // "updated_at": "2023-05-16T15:28:23Z"


            // {
            //     "token": "bbba0c33-6398-4580-b1f9-7ebedd63046a",
            //     "resource": "card_token",
            //     "attributes": {
            //         "card": {
            //             "is_active": true,
            //             "holder": "ROCKETLEADS LTDA",
            //             "bin": "549738",
            //             "last4": "8229",
            //             "brand": "mastercard"
            //         },
            //         "holder": {
            //             "name": "",
            //             "cpf": "",
            //             "foreignerNumber": "",
            //             "email": "",
            //             "contacts": [],
            //             "addresses": []
            //         },
            //         "validated_at": "",
            //         "expires_at": "2029-05-31 23:59:59",
            //         "created_at": "2023-08-30 01:30:12",
            //         "updated_at": "2023-08-30 01:30:12"
            //     }
            // }


            // if (empty($cards) || empty($card)) throw new PagarmeExceptions\EmptyCardsException;

            $response = [
                "status" => "success",
                "code" => 0,
                "message" => "Cliente localizado.",
                "data" => compact("cards")
            ];
        }

        catch (CustomerExceptions\CustomerNotFoundException)
        {
            $response = [
                "status" => "error",
                "code" => 1,
                "message" => "Cliente não autenticado."
            ];
        }

        catch (PagarmeExceptions\CustomerNotFoundException|IpagExceptions\CustomerNotFoundException)
        {
            $response = [
                "status" => "error",
                "code" => 2,
                "message" => "O cliente não possui cadastro no gateway."
            ];
        }

        catch (PagarmeExceptions\EmptyCustomerIDException|IpagExceptions\EmptyCustomerIDException)
        {
            $response = [
                "status" => "error",
                "code" => 3,
                "message" => "Não foi encontrado o id de usuário deste cliente."
            ];
        }

        catch (PagarmeExceptions\EmptyCardsException|IpagExceptions\EmptyCardsException)
        {
            $response = [
                "status" => "error",
                "code" => 4,
                "message" => "Nenhum cartão encontrado."
            ];
        }

        finally
        {
            Response::json($response);
        }
    }
}