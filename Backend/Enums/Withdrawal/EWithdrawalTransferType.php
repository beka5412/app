<?php 

namespace Backend\Enums\Withdrawal;

enum EWithdrawalTransferType : String
{
    case BANK = 'bank';
    case PIX = 'pix';
}