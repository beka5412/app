<?php

namespace Backend\Types\Iugu\Events;

enum EIuguSubscription: string
{
    case CREATED = 'subscription.created';
    case RENEWED = 'subscription.renewed';
    case ACTIVATED = 'subscription.activated';
    case EXPIRED = 'subscription.expired';
    case SUSPENDED = 'subscription.suspended';
    case CHANGED = 'subscription.changed';
}