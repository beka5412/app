<?php

namespace Backend\Controllers\Cronjobs\User;

use Backend\Entities\Abstracts\SellerBalance;
use Backend\Enums\AwardRequest\EAwardRequestStatus;
use Backend\Enums\EmailTemplate\EEmailTemplatePath;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Models\Award;
use Backend\Models\AwardRequest;
use Backend\Models\Balance;
use Backend\Models\User;
use Backend\Models\UserAddress;
use Backend\Types\Response\EResponseDataStatus;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;

class AwardController
{
    public function handle(Request $request)
    {
        set_time_limit(0);

        $awards = Award::orderBy('amount', 'ASC')->get();
        if (!$awards)
        {
            return Response::json(
                new ResponseData([
                    'status' => EResponseDataStatus::ERROR,
                    'message' => 'No awards found.'
                ]),
                new ResponseStatus('400 Bad Request'), 1
            );
        }

        $awards_array = [...$awards];

        $balances = Balance::all();

        $executed = false;

        foreach ($balances as $balance)
        {
            ['user_id' => $user_id, 'amount' => $amount] = $balance;
            ['email' => $email, 'name' => $name] = User::find($user_id);

            $biggest_award = AwardRequest::join('awards', 'award_requests.award_id', '=', 'awards.id')
            ->where('user_id', $user_id)->orderBy('awards.amount', 'DESC')->first();

            $email_data = [
                "site_url" => site_url(),
                "platform" => site_name(),
                "username" => $name
            ];

            if ($biggest_award)
            {
                $next_award = [...array_filter($awards_array, function($award) use ($biggest_award) {
                    return $award->amount > $biggest_award->award->amount;
                })][0] ?? null;

                $user_address = UserAddress::where('user_id', $user_id)->first();

                if ($amount >= $biggest_award->award->amount && $amount >= $next_award->amount)
                {
                    $next_award_amount = $next_award->amount;

                    AwardRequest::create([
                        'user_id' => $user_id,
                        'award_id' => $next_award->id,
                        'status' => EAwardRequestStatus::PENDING,
                        'user_address_id' => $user_address->id ?? null
                    ]);

                    $executed = true;

                    $email_data['totalk'] = currencyk($next_award_amount);
                    $email_data['total'] = number_to_currency_by_symbol($next_award_amount, 'brl');
                    $email_data['symbol'] = currency_code_to_symbol('brl');

                    $template = null;
                    if ($next_award_amount >= 10_000 && $next_award_amount < 100_000) $template = EEmailTemplatePath::AWARD_10K;
                    else if ($next_award_amount >= 100_000 && $next_award_amount < 500_000) $template = EEmailTemplatePath::AWARD_100K;
                    else if ($next_award_amount >= 500_000 && $next_award_amount < 1_000_000) $template = EEmailTemplatePath::AWARD_500K;
                    else if ($next_award_amount >= 1_000_000 && $next_award_amount < 10_000_000) $template = EEmailTemplatePath::AWARD_1M;
                    else if ($next_award_amount >= 10_000_000 && $next_award_amount < 100_000_000) $template = EEmailTemplatePath::AWARD_10M;
                    else if ($next_award_amount >= 100_000_000 && $next_award_amount < 1_000_000_000) $template = EEmailTemplatePath::AWARD_100M;

                    if ($template) send_email($email, $email_data, $template, 'pt_BR');
                }
            }
            else
            {
                if ($awards_array && $awards_array[0]?->amount > 0 && $balance->amount >= $awards_array[0]?->amount)
                {
                    $award_amount = $awards_array[0]?->amount;

                    AwardRequest::create([
                        'user_id' => $user_id,
                        'award_id' => $awards_array[0]->id,
                        'status' => EAwardRequestStatus::PENDING
                    ]);

                    $executed = true;

                    $email_data['totalk'] = currencyk($award_amount);
                    $email_data['total'] = number_to_currency_by_symbol($award_amount, 'brl');
                    $email_data['symbol'] = currency_code_to_symbol('brl');

                    send_email($email, $email_data, EEmailTemplatePath::AWARD_10K, 'pt_BR');
                }
            }
        }
        
        return Response::json(
            new ResponseData([
                'status' => $executed ? EResponseDataStatus::SUCCESS : EResponseDataStatus::ERROR,
                'message' => $executed ? 'Award requested.' : 'Nothing to run now.'
            ]),
            new ResponseStatus($executed ? '200 OK' : '404 Not Found'), 1
        );
    }
}
