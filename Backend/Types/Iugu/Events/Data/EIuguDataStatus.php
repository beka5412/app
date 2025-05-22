<?php

namespace Backend\Types\Iugu\Events\Data;

enum EIuguDataStatus: string
{
    case PAID = 'paid';
    case CANCELED = 'canceled';
    case PARTIALLY_PAID = 'partially_paid';
    case REFUNDED = 'refunded';
    case EXPIRED = 'expired';
    case AUTHORIZED = 'authorized';
    case EXTERNALLY_PAID = 'externally_paid';
    case IN_PROTEST = 'in_protest';
    case CHARGEBACK = 'chargeback';
}
