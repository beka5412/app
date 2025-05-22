<?php 

namespace Backend\Enums\Coupon;

enum ECouponStatus : String
{
    case PUBLISHED = 'published';
    case DISABLED = 'disabled';
    case DRAFT = 'draft';
}