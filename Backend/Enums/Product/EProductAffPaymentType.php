<?php

namespace Backend\Enums\Product;

enum EProductAffPaymentType : String
{
    case PERCENT = 'percent';
    case PRICE = 'price';
}