<?php

namespace Backend\Enums\Purchase;

enum EPurchaseStatus : String
{
    case ACTIVE = 'active';
    case CANCELED = 'canceled';
}