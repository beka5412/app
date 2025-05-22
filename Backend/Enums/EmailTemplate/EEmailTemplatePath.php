<?php 

namespace Backend\Enums\EmailTemplate;

enum EEmailTemplatePath: string
{
    case PURCHASE_APPROVED = 'stripe/customer/approvedPurchase';
    case PURCHASE_APPROVED_WITH_PASSWORD = 'stripe/customer/approvedPurchaseWithPassword';
    case AWARD_10K = 'awards/award10k';
    case AWARD_100K = 'awards/award100k';
    case AWARD_500K = 'awards/award500k';
    case AWARD_1M = 'awards/award1M';
    case AWARD_10M = 'awards/award10M';
    case AWARD_100M = 'awards/award100M';
    case APPROVED_WITHDRAWAL = 'withdrawal/approved';
    case CANCELED_WITHDRAWAL = 'withdrawal/canceled';
    case REQUESTED_WITHDRAWAL = 'withdrawal/requested';
    case APPROVED_PRODUCT = 'product/approved';
    case REJECTED_PRODUCT = 'product/rejected';
    case REQUESTED_PRODUCT = 'product/requested';
    case CONFIRMED_KYC = 'kyc/confirmed';
    case REJECTED_KYC = 'kyc/rejected';
    case REQUESTED_KYC = 'kyc/requested';
    case ACCOUNT_UNDER_ANALYSIS = 'user/accountUnderAnalysis';
}
