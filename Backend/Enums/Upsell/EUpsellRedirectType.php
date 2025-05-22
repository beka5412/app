<?php 

namespace Backend\Enums\Upsell;

enum EUpsellRedirectType : string
{
    case EXTERNAL = 'external';
    case PURCHASES = 'purchases';
}