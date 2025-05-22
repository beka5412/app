<?php

namespace Backend\Enums\PagarMe;

abstract class EPagarMePaymentMethod
{
    const CREDIT_CARD = 'credit_card';
    const PIX = 'pix';
    const BILLET = 'boleto';
}