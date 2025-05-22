<?php 

namespace Backend\Enums\Kyc;

enum EKycStatus : String
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case REJECTED = 'rejected';
}