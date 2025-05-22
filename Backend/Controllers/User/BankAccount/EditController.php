<?php

namespace Backend\Controllers\User\BankAccount;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\BankAccount;
use Backend\Exceptions\BankAccount\
{
    EmptyNameException,
    EmptyDocException,
    EmptyBankException,
    EmptyTypeException,
    EmptyAgencyException,
    EmptyAccountException,
    EmptyDigitException,
    EmptyPixTypeException,
    EmptyPixException
};

class EditController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Editar conta bancária';
        $this->context = 'dashboard';
        // $this->indexFile = 'frontend/view/user/products/checkouts/editView.php';
        $this->user = user();
    }

    // public function index(Request $request, $product_id, $checkout_id)
    // {
    //     $title = $this->title;
    //     $context = $this->context;
    //     $user = $this->user;
    //     $checkout = Checkout::where('id', $checkout_id)->where('user_id', $user->id)->first();

    //     View::render($this->indexFile, compact('title', 'context', 'user', 'checkout'));
    // }

    // public function element(Request $request)
    // {
    //     $body = $request->pageParams();
    //     $product_id = $body?->product_id;
    //     $checkout_id = $body?->checkout_id;
    //     $title = $this->title;
    //     $context = $this->context;
    //     $user = $this->user;
    //     $checkout = Checkout::where('id', $checkout_id)->where('user_id', $user->id)->first();

    //     View::response($this->indexFile, compact('title', 'context', 'user', 'checkout'));
    // }

    public function update(Request $request, $id)
    {
        $user = $this->user;
        $response = [];
        $body = $request->json();
        $name = $body->name ?? '';
        $doc = $body->doc ?? '';
        $bank = $body->bank ?? '';
        $type = $body->type ?? '';
        $agency = $body->agency ?? '';
        $account = $body->account ?? '';
        $digit = $body->digit ?? '';
        $pix_type = $body->pix_type ?? '';
        $pix = $body->pix ?? '';

        try
        {
            $bank_account = BankAccount::where('user_id', $user->id)->first();

            if (empty($bank_account)) 
            {
                $bank_account = new BankAccount;
                $bank_account->user_id = $user->id;
            }

            if (!$name) throw new EmptyNameException;
            if (!$doc) throw new EmptyDocException;
            if (!$bank) throw new EmptyBankException;
            if (!$type) throw new EmptyTypeException;
            if (!$agency) throw new EmptyAgencyException;
            if (!$account) throw new EmptyAccountException;
            // if (!$digit) throw new EmptyDigitException;s
            // if (!$pix_type) throw new EmptyPixTypeException;
            // if (!$pix) throw new EmptyPixException;

            $bank_account->name = $name;
            $bank_account->doc = $doc;
            $bank_account->bank_id = $bank;
            $bank_account->type = $type;
            $bank_account->agency = $agency;
            $bank_account->account = $account;
            if ($digit) $bank_account->digit = $digit;
            $bank_account->pix_type = $pix_type;
            $bank_account->pix = $pix;
            $bank_account->save(); // PDOException

            $response = ["status" => "success", "message" => "Conta bancária atualizada com sucesso.", "data" => $bank_account];
        }

        catch (EmptyNameException $ex)
        {
            $response = ["status" => "error", "message" => "O nome não pode estar em branco."];
        }

        catch (EmptyDocException $ex)
        {
            $response = ["status" => "error", "message" => "O documento não pode estar em branco."];
        }

        catch (EmptyBankException $ex)
        {
            $response = ["status" => "error", "message" => "O banco não pode estar em branco."];
        }

        catch (EmptyTypeException $ex)
        {
            $response = ["status" => "error", "message" => "O tipo de conta não pode estar em branco."];
        }

        catch (EmptyAgencyException $ex)
        {
            $response = ["status" => "error", "message" => "A agência não pode estar em branco."];
        }

        catch (EmptyAccountException $ex)
        {
            $response = ["status" => "error", "message" => "A conta não pode estar em branco."];
        }

        catch (EmptyDigitException $ex)
        {
            $response = ["status" => "error", "message" => "O dígito não pode estar em branco."];
        }

        catch (EmptyPixTypeException $ex)
        {
            $response = ["status" => "error", "message" => "O tipo de conta pix não pode estar em branco."];
        }

        catch (EmptyPixException $ex)
        {
            $response = ["status" => "error", "message" => "O pix não pode estar em branco."];
        }

        finally
        {
            Response::json($response);
        }
    }
}