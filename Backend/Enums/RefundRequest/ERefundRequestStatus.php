<?php 

namespace Backend\Enums\RefundRequest;

enum ERefundRequestStatus : String
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case CANCELED = 'canceled';
}