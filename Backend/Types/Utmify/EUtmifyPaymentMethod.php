<?php

namespace Backend\Types\Utmify;

enum EUtmifyPaymentMethod: string
{
    case CREDIT_CARD = 'credit_card';
    case BOLETO = 'boleto';
    case PIX = 'pix';
    case PAYPAL = 'paypal';
    case FREE_PRICE = 'free_price';
}
