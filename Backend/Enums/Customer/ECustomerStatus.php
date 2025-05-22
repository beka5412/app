<?php 

namespace Backend\Enums\Customer;

enum ECustomerStatus : String
{
    case ACTIVE = 'active';
    case DISABLED = 'disabled';
    case KICKED = 'kicked';
    case BANNED = 'banned';
}