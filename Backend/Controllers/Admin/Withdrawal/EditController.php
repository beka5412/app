<?php

namespace Backend\Controllers\Admin\Withdrawal;

use Backend\App;
use Backend\Controllers\Controller\AFrontendController;
use Backend\Controllers\Controller\TFrontendController;
use Backend\Enums\EmailTemplate\EEmailTemplatePath;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Exceptions\Withdrawal\WithdrawalNotFoundException;
use Backend\Exceptions\Balance\EmptyBalanceException;
use Backend\Exceptions\Balance\InsufficientBalanceException;
use Backend\Exceptions\Withdrawal\WithdrawalInvalidException;
use Backend\Enums\Withdrawal\EWithdrawalStatus;
use Backend\Models\Withdrawal;
use Backend\Models\Balance;

class EditController extends AFrontendController
{
    use TFrontendController;

    public string $title = 'Editar saque';
    public string $context = 'dashboard';

    public function update(Request $request, $id)
    {
        $user = $this->user;
        $response = [];
        $body = $request->json();
        $status = $body->status;

        try
        {
            $withdrawal = Withdrawal::where('id', $id)->first();
            if (empty($withdrawal)) throw new WithdrawalNotFoundException;

            $prev_answered = $withdrawal->answered ?? false;
            if ($prev_answered) throw new WithdrawalInvalidException;

            $withdrawal->status = $status;
            // uma vez que a solicitacao foi respondida aceitando ou rejeitando, nao pode ser mais possivel mexer nela
            $withdrawal->answered = 1;
            $withdrawal->reason = $body->reason ?? '';
            $withdrawal->save();

            $balance = Balance::where('user_id', $withdrawal->user_id)->first();
            if (empty($balance))
            {
                $balance = new Balance;
                $balance->user_id = $withdrawal->user_id;
                $balance->save();
            }

            $calc_available = doubleval($balance->available);
            $calc_blocked = doubleval($balance->blocked);
            $calc_amount = doubleval($balance->amount);
            $calc_withdrawn = doubleval($balance->withdrawn);
            $calc_withdrawal_requested = doubleval($balance->withdrawal_requested) - doubleval($withdrawal->amount);

            // se for aprovado
            if ($status == EWithdrawalStatus::APPROVED->value)
            {
                if ($withdrawal->amount > $balance->blocked) throw new InsufficientBalanceException;

                // o saldo a ser sacado esta em blocked, se foi aceito, retirar esse valor de blocked
                $calc_blocked = doubleval($balance->blocked) - doubleval($withdrawal->amount);
                $calc_amount = doubleval($balance->amount) - doubleval($withdrawal->amount);
                $calc_withdrawn = doubleval($balance->withdrawn) + doubleval($withdrawal->amount);
            }

            // se for rejeitado
            if ($status == EWithdrawalStatus::CANCELED->value)
            {
                // desfaz a solicitacao
                $calc_available = doubleval($balance->available) + doubleval($withdrawal->amount);
                $calc_blocked = doubleval($balance->blocked) - doubleval($withdrawal->amount);
            }

            $balance->available = $calc_available;
            $balance->blocked = $calc_blocked;
            $balance->amount = $calc_amount;
            $balance->withdrawn = $calc_withdrawn;
            $balance->withdrawal_requested = $calc_withdrawal_requested;
            $balance->save();

            if ($status == EWithdrawalStatus::APPROVED->value)
            {
                $email_data = [
                    'site_url' => site_url(),
                    'platform' => site_name(),
                    'username' => $user->name,
                ];
                $email_data['total'] = number_to_currency_by_symbol($withdrawal->amount, 'brl');
                $email_data['symbol'] = currency_code_to_symbol('brl')->value;
                $email_data['amount'] = "$email_data[symbol] $email_data[total]";
    
                send_email($user->email, $email_data, EEmailTemplatePath::APPROVED_WITHDRAWAL, 'pt_BR');
            }
            else if ($status == EWithdrawalStatus::CANCELED->value)
            {
                $email_data = [
                    'site_url' => site_url(),
                    'platform' => site_name(),
                    'username' => $user->name,
                ];
                $email_data['total'] = number_to_currency_by_symbol($withdrawal->amount, 'brl');
                $email_data['symbol'] = currency_code_to_symbol('brl')->value;
                $email_data['amount'] = "$email_data[symbol] $email_data[total]";
    
                send_email($user->email, $email_data, EEmailTemplatePath::CANCELED_WITHDRAWAL, 'pt_BR');
            }

            $response = ["status" => "success", "message" => "Solicitação de saque atualizada com sucesso."];
        }

        catch (WithdrawalNotFoundException $ex)
        {
            $response = ["status" => "error", "message" => "Solicitação de saque não encontrada."];
        }

        catch (InsufficientBalanceException $ex)
        {
            $response = ["status" => "error", "message" => "O usuário está com saldo insuficiente."];
        }

        catch (WithdrawalInvalidException $ex)
        {
            $response = ["status" => "error", "message" => "Essa solicitação já teve uma resposta."];
        }

        // catch(EmptyBalanceException $ex)
        // {
        //     $response = ["status" => "error", "message" => "O objeto que representa o saldo do usuário não foi encontrado."];
        // }

        finally
        {
            Response::json($response);
        }
    }
}
