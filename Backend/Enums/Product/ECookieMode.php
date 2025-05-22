<?php

namespace Backend\Enums\Product;

enum ECookieMode : String
{
    case FIRST_CLICK = 'first_click';
    case LAST_CLICK = 'last_click';
}