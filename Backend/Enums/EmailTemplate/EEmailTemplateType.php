<?php 

namespace Backend\Enums\EmailTemplate;

enum EEmailTemplateType: string
{
    case PURCHASE_APPROVED = 'purchase.approved';
    case PURCHASE_APPROVED_WITH_PASSWORD = 'purchase.approved_with_password';
}