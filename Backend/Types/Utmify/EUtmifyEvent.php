<?php

namespace Backend\Types\Utmify;

enum EUtmifyEvent: string
{
    case WAITING_PAYMENT = 'waiting_payment';
    case PAID = 'paid';
    case REFUSED = 'refused';
    case REFUNDED = 'refunded';
    case CHARGEDBACK = 'chargedback';
}