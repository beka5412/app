<?php 

namespace Backend\Enums\Checkout;

enum ECheckoutStatus : String
{
    case PUBLISHED = 'published';
    case DISABLED = 'disabled';
    case DRAFT = 'draft';
}