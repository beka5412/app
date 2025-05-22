<?php
declare(strict_types=1);
namespace Backend\Entities\Abstracts;

use Backend\Enums\EmailTemplate\EEmailTemplatePath;
use Backend\Enums\Order\EOrderStatusDetail;
use Backend\Models\Balance;
use Backend\Models\BalanceHistory;
use Backend\Models\FeedEmail;
use Backend\Models\Invoice;
use Backend\Models\Order;
use Backend\Models\User;
use Backend\Types\SellerCredit\ESellerCreditQueueStatus;
use Backend\Types\SellerCredit\ESellerCredityBodyType;
use Backend\Types\SellerCredit\SellerCreditBody;
use Backend\Types\SellerCredit\SellerCreditBodyWhere;
use Backend\Types\SellerCredit\SellerQueueUpdateData;
use Backend\Types\SellerCredit\SellerQueueUpdateWhere;
use Backend\Types\Stripe\Entity\EStripePaymentIntentStatus;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Collection;

class SellerBalance
{
    public static function credit(Order $order): void
    {
        $balance = Balance::where('user_id', $order->user_id)->first();

        if (empty($balance))
        {
            $balance = new Balance;
            $balance->user_id = $order->user_id;
        }

        $balance->amount += $order->total_seller;
        $balance->future_releases += $order->total_seller;
        $balance->save();

        self::futureRelease($order, $balance);
    }
    
    public static function debit(?Order $order): void
    {
        $balance = Balance::where('user_id', $order->user_id)->first();

        if (empty($balance))
        {
            $balance = new Balance;
            $balance->user_id = $order->user_id;
        }

        $balance->amount -= $order->total_seller;

        if ($sent_frs = self::getSentFutureReleases($order))
        {
            foreach ($sent_frs as $future_release)
            {
                $fr_data = json_decode($future_release->data);
                $balance->available -= $fr_data->amount;

                $balance_history = new BalanceHistory;
                $balance_history->user_id = $order->user_id;
                $balance_history->operation = 'D';
                $balance_history->type = 'available';
                $balance_history->amount = $fr_data->amount;
                $balance_history->save();
            }
        }

        if ($waiting_frs = self::getWaitingFutureReleases($order))
        {
            foreach ($waiting_frs as $future_release)
            {
                $fr_data = json_decode($future_release->data);
                $balance->future_releases -= $fr_data->amount;

                $balance_history = new BalanceHistory;
                $balance_history->user_id = $order->user_id;
                $balance_history->operation = 'D';
                $balance_history->type = 'future_release';
                $balance_history->amount = $fr_data->amount;
                $balance_history->save();

                if (($fr_data->type ?? false) === ESellerCredityBodyType::RESERVED_AS_GUARANTEE->value)
                {
                    $balance->reserved_as_guarantee -= $fr_data->amount;

                    $balance_history = new BalanceHistory;
                    $balance_history->user_id = $order->user_id;
                    $balance_history->operation = 'D';
                    $balance_history->type = 'reserved_as_guarantee';
                    $balance_history->amount = $fr_data->amount;
                    $balance_history->save();
                }

                self::cancelFutureRelease($order);
            }
        }

        $balance->save();
    }

    public static function chargebackPercent(string|int $user_id): float
    {
        $approved_orders = Order::select(DB::raw('SUM(total) AS total'))
            ->where('user_id', $user_id)
            ->where('status_details', EOrderStatusDetail::APPROVED)
            ->where('created_at', '>', DB::raw('NOW() - INTERVAL 90 DAY'))
            ->where('created_at', '<', DB::raw('NOW()'))
            ->first()->total ?? 0;

        $chargedback_orders = Order::select(DB::raw('SUM(total) AS total'))
            ->where('user_id', $user_id)
            ->where('status_details', EOrderStatusDetail::CHARGEDBACK)
            ->where('created_at', '>', DB::raw('NOW() - INTERVAL 90 DAY'))
            ->where('created_at', '<', DB::raw('NOW()'))
            ->first()->total ?? 0;

        $approved_subscriptions = Invoice::whereNotNull('gateway_invoice_id')
            ->whereHas('payment_intent', function ($query)
            {
                $query->where('status', EStripePaymentIntentStatus::SUCCEEDED);
            })
            ->where('created_at', '>', DB::raw('NOW() - INTERVAL 90 DAY'))
            ->where('created_at', '<', DB::raw('NOW()'))
            ->where('paid', 1)->get();
        $approved_subscriptions = array_map(fn ($row) => ($row->order->total ?? 0), [...$approved_subscriptions]);
        $approved_subscriptions = array_accumulator($approved_subscriptions, fn ($sum, $current) => $sum + $current, 0);

        $chargedback_subscriptions = Invoice::whereNotNull('gateway_invoice_id')
            ->whereHas('payment_intent', function ($query)
            {
                $query->where('status', EStripePaymentIntentStatus::PAYMENT_FAILED);
            })
            ->where('created_at', '>', DB::raw('NOW() - INTERVAL 90 DAY'))
            ->where('created_at', '<', DB::raw('NOW()'))
            ->get();
        $chargedback_subscriptions = array_map(fn ($row) => $row->order->total ?? 0, [...$chargedback_subscriptions]);
        $chargedback_subscriptions = array_accumulator($chargedback_subscriptions, fn ($sum, $current) => $sum + $current, 0);

        $chargedback = $chargedback_orders + $chargedback_subscriptions;
        $approved = $approved_orders + $approved_subscriptions;

        $percent = $approved === 0 ? 0 : $chargedback / $approved;

        return $percent;
    }

    public static function futureRelease(Order $order, Balance $balance): void
    {
        ['user_id' => $user_id, 'total_seller' => $amount] = $order;

        $user = User::find($order->user_id);
        $chargeback_percent = ($user->chargeback_percent ?? 0) * 100;

        $chunk_1 = 0;
        $chunk_2 = 0;
        $chunk_3 = 0;
        $chunk_1_percent = 0;
        $chunk_2_percent = 0;
        $chunk_3_percent = 0;

        if ($chargeback_percent < 1)
        {
            $chunk_1_percent = (90 / 100);
            $chunk_1 = $amount * $chunk_1_percent;
            $chunk_3_percent = (10 / 100);
            $chunk_3 = $amount * $chunk_3_percent;
        }

        else if ($chargeback_percent < 3)
        {
            $chunk_1_percent = (65 / 100);
            $chunk_1 = $amount * $chunk_1_percent;
            $chunk_2_percent = (25 / 100);
            $chunk_2 = $amount * $chunk_2_percent;
            $chunk_3_percent = (10 / 100);
            $chunk_3 = $amount * $chunk_3_percent;
        }

        else if ($chargeback_percent < 5)
        {
            $chunk_1_percent = (50 / 100);
            $chunk_1 = $amount * $chunk_1_percent;
            $chunk_2_percent = (40 / 100);
            $chunk_2 = $amount * $chunk_2_percent;
            $chunk_3_percent = (10 / 100);
            $chunk_3 = $amount * $chunk_3_percent;
        }

        else if ($chargeback_percent < 10)
        {
            $chunk_1_percent = (10 / 100);
            $chunk_1 = $amount * $chunk_1_percent;
            $chunk_2_percent = (75 / 100);
            $chunk_2 = $amount * $chunk_2_percent;
            $chunk_3_percent = (15 / 100);
            $chunk_3 = $amount * $chunk_3_percent;
        }

        else
        {
            $user->account_under_analysis = 1;
            $user->save();
         
            $email_data = [
                'site_url' => site_url(),
                'platform' => site_name(),
                'username' => $user->name,
            ];

            send_email($user->email, $email_data, EEmailTemplatePath::ACCOUNT_UNDER_ANALYSIS, 'pt_BR');
        }

        if ($chunk_1)
        {
            $now = date("Y-m-d H:i:s");
            $days = get_setting('sales.credit_card.payout.available_at') ?: '7 days';
            $info_payment_method = $order->gateway;
            
            if ($info_payment_method === 'NoxPay') {
                $days = '0 days';
            }
            
            $date = date('Y-m-d H:i:s', strtotime("$now + $days"));
            SellerCreditQueue::push(
                json_encode(
                    new SellerCreditBody([
                        'order_id' => $order->id,
                        'user_id' => $user_id,
                        'amount' => $chunk_1,
                        'percent' => $chunk_1_percent,
                        'rate' => $chargeback_percent / 100
                    ])
                ),
                $date
            );
            
            $balance_history = new BalanceHistory;
            $balance_history->user_id = $order->user_id;
            $balance_history->operation = 'C';
            $balance_history->type = 'future_release';
            $balance_history->amount = $chunk_1;
            $balance_history->scheduled_at = $date;
            $balance_history->save();
        }

        if ($chunk_2)
        {
            $date = date('Y-m-d H:i:s', strtotime(today() . ' + 1 month'));
            SellerCreditQueue::push(
                json_encode(
                    new SellerCreditBody([
                        'order_id' => $order->id,
                        'user_id' => $user_id,
                        'amount' => $chunk_2,
                        'percent' => $chunk_2_percent,
                        'rate' => $chargeback_percent / 100
                    ])
                ),
                $date
            );
            
            $balance_history = new BalanceHistory;
            $balance_history->user_id = $order->user_id;
            $balance_history->operation = 'C';
            $balance_history->type = 'future_release';
            $balance_history->amount = $chunk_2;
            $balance_history->scheduled_at = $date;
            $balance_history->save();
        }

        if ($chunk_3)
        {
            $date = date('Y-m-d H:i:s', strtotime(today() . ' + 1 month'));
            SellerCreditQueue::push(
                json_encode(
                    new SellerCreditBody([
                        'order_id' => $order->id,
                        'user_id' => $user_id,
                        'amount' => $chunk_3,
                        'percent' => $chunk_3_percent,
                        'rate' => $chargeback_percent / 100,
                        'type' => ESellerCredityBodyType::RESERVED_AS_GUARANTEE
                    ])
                ),
                $date
            );
            
            $balance_history = new BalanceHistory;
            $balance_history->user_id = $order->user_id;
            $balance_history->operation = 'C';
            $balance_history->type = 'future_release';
            $balance_history->amount = $chunk_3;
            $balance_history->scheduled_at = $date;
            $balance_history->save();

            $balance->reserved_as_guarantee += $chunk_3;
            $balance->save();
        }
    }

    public static function cancelFutureRelease(Order $order): void
    {
        SellerCreditQueue::updateWhere(
            new SellerQueueUpdateWhere([
                'data' => new SellerCreditBodyWhere(['order_id' => $order->id]),
                'status' => ESellerCreditQueueStatus::WAITING
            ]),
            new SellerQueueUpdateData(['status' => ESellerCreditQueueStatus::CANCELED])
        );
    }

    public static function getSentFutureReleases(Order $order): Collection
    {
        return (
            SellerCreditQueue::getWhere(
                new SellerQueueUpdateWhere([
                    'data' => new SellerCreditBodyWhere(['order_id' => $order->id]),
                    'status' => ESellerCreditQueueStatus::SENT
                ])
            )
        );
    }

    public static function getWaitingFutureReleases(Order $order): Collection
    {
        return (
            SellerCreditQueue::getWhere(
                new SellerQueueUpdateWhere([
                    'data' => new SellerCreditBodyWhere(['order_id' => $order->id]),
                    'status' => ESellerCreditQueueStatus::WAITING
                ])
            )
        );
    }

    public static function taxChargeback(Order $order): void
    {
        $balance = Balance::where('user_id', $order->user_id)->first();
        if (empty($balance)) return;

        $chargeback_fee = get_setting('chargeback_fee');

        $balance->available -= $order->total * $chargeback_fee;
        $balance->save();

        $balance_history = new BalanceHistory;
        $balance_history->user_id = $order->user_id;
        $balance_history->operation = 'D';
        $balance_history->type = 'available';
        $balance_history->amount = $order->total * $chargeback_fee;
        $balance_history->description = 'Chargeback fee';
        $balance_history->save();
    }
}
