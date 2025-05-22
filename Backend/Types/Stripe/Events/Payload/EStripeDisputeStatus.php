<?php

namespace Backend\Types\Stripe\Events\Payload;

enum EStripeDisputeStatus: string
{
    case LOST = 'lost';
    case WARNING_CLOSED = 'warning_closed';
    case WON = 'won';
}
