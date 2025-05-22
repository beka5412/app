<?php

namespace Backend\Enums\Product;

enum EProductPaymentType : String
{
    case UNIQUE = 'unique';
    case RECURRING = 'recurring';
}