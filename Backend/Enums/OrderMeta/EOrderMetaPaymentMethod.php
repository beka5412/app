<?php

namespace Backend\Enums\OrderMeta;

enum EOrderMetaPaymentMethod : string
{
    case CREDIT_CARD = 'credit_card';
    case PIX = 'pix';
    case BILLET = 'billet';
}