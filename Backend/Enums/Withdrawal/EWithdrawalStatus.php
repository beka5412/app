<?php

namespace Backend\Enums\Withdrawal;

enum EWithdrawalStatus : String
{
    case APPROVED = 'approved';
    case PENDING = 'pending';
    case CANCELED = 'canceled';
}