<?php 

namespace Backend\Enums\Kyc;

enum EKycType : String
{
    case ID = 'id';
    case PASSPORT = 'passport';
    case DRIVING_LICENSE = 'driving_license';
    case COMPANY = 'company';
}