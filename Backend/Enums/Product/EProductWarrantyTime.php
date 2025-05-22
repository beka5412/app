<?php 

namespace Backend\Enums\Product;

enum EProductWarrantyTime : Int
{
    case ONE_WEEK = 7;
    case TWO_WEEKS = 14;
    case THREE_WEEKS = 21;
    case ONE_MONTH = 30;
}