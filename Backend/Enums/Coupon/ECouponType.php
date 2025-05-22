<?php 

namespace Backend\Enums\Coupon;

enum ECouponType : String
{
    case PERCENT = 'percent';
    case PRICE = 'price';    
}