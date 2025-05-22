<?php

namespace Backend\Types\Stripe\Events;

enum EStripeInvoiceItem: string
{
    case CREATED = 'invoiceitem.created'; // Occurs whenever an invoice item is created.
    case DELETED = 'invoiceitem.deleted'; // Occurs whenever an invoice item is deleted.
}
