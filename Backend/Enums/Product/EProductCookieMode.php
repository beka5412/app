<?php

namespace Backend\Enums\Product;

enum EProductCookieMode : String
{
    case FIRST_CLICK = 'first_click';
    case LAST_CLICK = 'last_click';
}