<?php 

namespace Backend\Enums\Product;

enum EProductRequestStatus : string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
}
