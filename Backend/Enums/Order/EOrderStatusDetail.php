<?php

namespace Backend\Enums\Order;

enum EOrderStatusDetail: string
{
    case INITIATED = 'initiated';
    case PENDING = 'pending';
    case BILLET_PRINTED = 'billet_printed';
    case PIX_GENERATED = 'pix_generated';
    case CAPTURED = 'captured';
    case APPROVED = 'approved';
    case IN_ANALYSIS = 'in_analysis';
    case REFUNDED = 'refunded';
    case CHARGEDBACK = 'chargedback';
    case DISPUTED = 'disputed';
    case PRE_AUTHORIZED = 'pre_authorized';
    case REJECTED = 'rejected';
    case CANCELED = 'canceled';
}