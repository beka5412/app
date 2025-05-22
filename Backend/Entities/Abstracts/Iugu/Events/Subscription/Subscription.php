<?php

namespace Backend\Entities\Abstracts\Iugu\Events\Subscription;

use Backend\Http\Request;

class Subscription
{
    public function renewed(Request $request)
    {
        return (new SubscriptionRenewed)->response($request);
    }
}
