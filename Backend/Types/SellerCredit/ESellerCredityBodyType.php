<?php

namespace Backend\Types\SellerCredit;

enum ESellerCredityBodyType: string
{
    case AVAILABLE = 'available';
    case BLOCKED = 'blocked';
    case WITHDRAWN = 'withdrawn';
    case PENDING = 'pending';
    case AMOUNT = 'amount';
    case WITHDRAWAL_REQUESTED = 'withdrawal_requested';
    case FUTURE_RELEASES = 'future_releases';
    case RESERVED_AS_GUARANTEE = 'reserved_as_guarantee';
}