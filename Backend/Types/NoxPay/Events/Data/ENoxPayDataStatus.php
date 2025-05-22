<?php

namespace Backend\Types\NoxPay\Events\Data;

enum ENoxPayDataStatus: string
{
    case PAID = 'PAID';
    case APPROVED = 'APPROVED';
    case WAITING_PAYMENT = 'WAITING_PAYMENT';
}