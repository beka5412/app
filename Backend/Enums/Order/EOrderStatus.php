<?php

namespace Backend\Enums\Order;

enum EOrderStatus: string
{
    case INITIATED = 'initiated';
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case CANCELED = 'canceled';
    case REJECTED = 'rejected';
}