<?php

namespace Backend\Enums\Subscription;

enum ESubscriptionStatus : String
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case CANCELED = 'canceled';
}