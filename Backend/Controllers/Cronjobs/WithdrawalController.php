<?php

namespace Backend\Controllers\Cronjobs;
use Backend\Models\Withdrawal;
use Backend\Models\Balance;

class WithdrawalController
{
    public function wook()
    {
        $now = date("Y-m-d H:i:s");
        $withdrawals = Withdrawal::where('expires_at', '<=', $now)->where('status', 'pending')->get();

        foreach ($withdrawals as $withdrawal)
        {
            $withdrawal->status = 'canceled';
            $withdrawal->responsible = 'system';
            $withdrawal->save();
            echo "$withdrawal->id [CANCELED]\n";

            $user_id = $withdrawal->user_id;
            $balance = Balance::where('user_id', $user_id)->first();
            if (empty($balance)) continue;

            // devolve saldo solicitado para saque que nao foi atendido
            $balance->available += $withdrawal->amount;
            $balance->blocked -= $withdrawal->amount;
            $balance->withdrawal_requested -= $withdrawal->amount;
            $balance->save();
        }
    }
}