<?php

namespace Backend\Controllers\Cronjobs\User;

use Backend\Entities\Abstracts\SellerBalance;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Models\User;
use Backend\Types\Response\EResponseDataStatus;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;

class ChargebackPercentController
{
    public function handle(Request $request)
    {
        set_time_limit(0);

        $users = User::all();

        foreach ($users as $user)
        {
            $user->chargeback_percent = SellerBalance::chargebackPercent($user->id);
            $user->save();
        }
        
        return Response::json(
            new ResponseData([
                'status' => EResponseDataStatus::SUCCESS,
                'message' => 'Successfully updated chargeback rates.'
            ]),
            new ResponseStatus('200 OK'), 1
        );
    }
}
