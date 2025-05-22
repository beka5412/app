<?php

namespace Backend\Enums\IPag;

abstract class EIPagPaymentStatus
{
    const CREATED = '1';
    const PIX_GENERATED = '1';
    const PRINTED_BOLETO = '2';
    const BILLET_PRINTED = '2';
    const CANCELED = '3';
    const IN_ANALYSIS = '4';
    const PRE_AUTHORIZED = '5';
    const DENIED = '7';
    const CAPTURED = '8';
    const CHARGEDBACK = '9';
    const DISPUTED = '10';
    const REFUNDED = '10';
}
