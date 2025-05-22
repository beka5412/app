<?php

namespace Backend\Types\Iugu\Events;

enum EIuguInvoice: string
{
    case CREATED = 'invoice.created';
    case STATUS_CHANGED = 'invoice.status_changed';
    case BANK_SLIP_STATUS = 'invoice.bank_slip_status';
    case REFUND = 'invoice.refund';
    case PAYMENT_FAILED = 'invoice.payment_failed';
    case DUE = 'invoice.due';
    case DUNNING_ACTION = 'invoice.dunning_action';
    case INSTALLMENT_RELEASED = 'invoice.installment_released';
    case RELEASED = 'invoice.released';
}
