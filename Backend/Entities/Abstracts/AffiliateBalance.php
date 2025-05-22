<?php

namespace Backend\Entities\Abstracts;

use Backend\Models\Balance;
use Backend\Enums\OrderMeta\EOrderMetaPaymentMethod;
use Backend\Models\Order;

class AffiliateBalance
{
    public static function credit(?Order $order, $meta_payment_method) : void
    {
        if (empty($order)) return;

        $now = date("Y-m-d H:i:s");

        $aff_balance = Balance::where('user_id', $order->aff_id)->first();
        if (empty($aff_balance))
        {
            $aff_balance = new Balance;
            $aff_balance->user_id = $order->aff_id;
        }

        $aff_balance->amount += (double) $order->total_aff;

        // se for no pix ou boleto, fica disponivel na hora
        if ($meta_payment_method == EOrderMetaPaymentMethod::PIX->value || $meta_payment_method == EOrderMetaPaymentMethod::BILLET->value)
        {
            $aff_balance->available += (double) $order->total_aff;

            $order->aff_was_credited = 1; // se o saldo foi pago para o vendedor 
            $order->aff_credited_at = $now; // data que o saldo vai ser creditado
            $order->queue_aff_credit = 0; // nao adiciona na fila de pagamento
            $order->save();
        }

        // se for no cartao, em 14 dias
        else if ($meta_payment_method == EOrderMetaPaymentMethod::CREDIT_CARD->value)
        {
            // inserir numa fila, caso passe 14 dias sem ter reembolso ou cancelamento
            // o valor sera marcado como pago ao vendedor e incrementado no saldo available

            $order->aff_was_credited = 0; // se o saldo foi pago para o vendedor 
            $days = get_setting('sales.credit_card.payout.available_at') ?: '14 days';
            $order->aff_credited_at = date('Y-m-d H:i:s', strtotime("$now + $days")); // data que o saldo vai ser creditado
            $order->queue_aff_credit = 1; // adiciona na fila de pagamento
            $order->save();

            $aff_balance->future_releases = doubleval($aff_balance->future_releases) + $order->total_aff;
        }

        $aff_balance->save();
    }
    public static function debit(?Order $order, $meta_payment_method) : void
    {
        if (empty($order)) return;

        $aff_balance = Balance::where('user_id', $order->aff_id)->first();
        if (!empty($aff_balance))
        {
            $aff_balance->amount -= (double) $order->total_aff;

            // so vai retirar do saldo disponivel caso tenha sido depositado la
            if ($order->aff_was_credited)
                $aff_balance->available -= (double) $order->total_aff;
            
            // se esta na fila para ser creditado futuramente e for compra no cartao de credito 
            if ($order->queue_aff_credit && $meta_payment_method == EOrderMetaPaymentMethod::CREDIT_CARD->value)
                $aff_balance->future_releases = doubleval($aff_balance->future_releases) - $order->total_aff;

            $aff_balance->save();
        }
        
        $order->queue_aff_credit = 0; // remove o vendedor da fila para receber o valor da venda
        $order->save();
    }
}