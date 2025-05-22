<?php

namespace Backend\Controllers\User\Withdrawal;

use Backend\App;
use Backend\Enums\EmailTemplate\EEmailTemplatePath;
use Backend\Exceptions\BankAccount\EmptyPixException;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Enums\Withdrawal\EWithdrawalTransferType;
use Backend\Exceptions\Withdrawal\MinimumWithdrawalException;
use Backend\Exceptions\Balance\InsufficientBalanceException;
use Backend\Services\BancoInter\Instance as BancoInter;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;
use Ezeksoft\PHPWriteLog\Log;
use Backend\Models\User;
use Backend\Models\Withdrawal;
use Backend\Models\Balance;
use Backend\Models\BancoInterRequest;
use Backend\Models\FeedEmail;
use Backend\Types\Response\EResponseDataStatus;

class IndexController
{
    public App $application;

    public $title = 'Withdrawal';
    public $context = 'dashboard';
    public User $user;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->user = user();
    }

    public function store(Request $request)
    {
        $user = $this->user;
        $body = $request->json();

        if ($user->account_under_analysis)
        {
            return Response::json(
                new ResponseData(['status' => EResponseDataStatus::ERROR, 'message' => 'O estado da sua conta não permite saques.']),
                new ResponseStatus('400 Bad Request')
            );
        }

        $transfer_type = $body->transfer_type ?? '';
        $amount = $body->amount ?? '';
        $minimum_withdrawal = (Double) get_setting('minimum_withdrawal');
        $w_fee = doubleval(get_setting('withdrawal_fee'));

        $response = [];

        try 
        {
            if ($amount < $minimum_withdrawal) throw new MinimumWithdrawalException;

            // verificar se tem saldo suficiente
            $balance = Balance::where('user_id', $user->id)->first();
            if (empty($balance)) 
            {
                $balance = new Balance;
                $balance->user_id = $user->id;
                $balance->save();
            }

            if ($amount > $balance->available) throw new InsufficientBalanceException;

            // NOTE:
            // ao solicitar, tira de disponivel e joga para bloqueado

            $calc_available = doubleval($balance->available) - $amount;
            $calc_blocked = doubleval($balance->blocked) + $amount;
            $calc_withdrawal_requested = doubleval($balance->withdrawal_requested) + $amount;

            $balance->available = $calc_available;
            $balance->blocked = $calc_blocked;
            $balance->withdrawal_requested = $calc_withdrawal_requested;
            $balance->save();

            $withdrawal = new Withdrawal;
            $withdrawal->user_id = $user->id;
            $withdrawal->amount = $amount;

            if ($transfer_type == EWithdrawalTransferType::BANK->value)
                $withdrawal->transfer_type = EWithdrawalTransferType::BANK;

            else if ($transfer_type == EWithdrawalTransferType::PIX->value)
                $withdrawal->transfer_type = EWithdrawalTransferType::PIX;

            $withdrawal->total = $amount - $w_fee;
            $withdrawal->expires_at = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")." + 3 days"));
            $withdrawal->save();

            $feed_emails = FeedEmail::all();
            foreach ($feed_emails as $feed_email)
            {
                $email_data = [
                    'site_url' => site_url(),
                    'platform' => site_name(),
                    'username' => $user->name,
                    'amount' => "R$ ".number_to_currency_by_symbol($withdrawal->amount, 'brl'),
                    'amount_with_fee' => "R$ ".number_to_currency_by_symbol($withdrawal->total, 'brl'),
                    'fee' => "R$ ".number_to_currency_by_symbol($w_fee, 'brl'),
                    'user_email' => $user->email,
                ];

                send_email($feed_email->email, $email_data, EEmailTemplatePath::REQUESTED_WITHDRAWAL, 'pt_BR');
            }


            /**
             * Tenta enviar uma confirmacao para a conta no banco inter para
             * fazer a transferencia para o usuario
             */

            // if ($transfer_type == EWithdrawalTransferType::PIX->value)
            // {
            //     if (empty($user->bank_account) || empty($recipient=$user->bank_account->pix)) throw new EmptyPixException;

            //     $bi_pix = BancoInter::pix($recipient, $withdrawal->total, "$user->email");
            //     (new Log)->write(base_path('logs/bancointer_request.log'), json_encode($bi_pix));

            //     $bi_request = new BancoInterRequest;
            //     $bi_request->status = 'pending';
            //     $bi_request->withdrawal_id = $withdrawal->id;
            //     $bi_request->external_transaction_id = $bi_pix->json?->codigoSolicitacao ?? '';
            //     $bi_request->save();

            //     // EM ABERTO: se nao tem pix, cadastrado, avisar de algum jeito que precisa resolicitar
            // }

            $response = ['status' => 'success', 'message' => 'Solicitação de saque realizada.'];
        }

        catch (InsufficientBalanceException)
        {
            $response = ['status' => 'error', 'message' => 'Saldo insuficiente.'];
        }

        catch (MinimumWithdrawalException)
        {
            $response = ['status' => 'error', 'message' => 'Saque mínimo não atingido.'];
        }

        catch (EmptyPixException)
        {
            $response = ['status' => 'error', 'message' => 'Você precisa configurar sua chave pix antes de solicitar essa transferência.'];
        }

        finally
        {
            Response::json($response);
        }
    }

    public function iugu(Request $request)
    {
        
    }
}
