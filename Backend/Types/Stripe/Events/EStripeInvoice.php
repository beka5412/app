<?php

namespace Backend\Types\Stripe\Events;

enum EStripeInvoice: string
{
    case CREATED = 'invoice.created'; // Occurs whenever a new invoice is created. To learn how webhooks can be used with this event, and how they can affect it, see Using Webhooks with Subscriptions.
    case DELETED = 'invoice.deleted'; // Occurs whenever a draft invoice is deleted. Note: This event is not sent for invoice previews.
    case FINALIZATION_FAILED = 'invoice.finalization_failed'; // Occurs whenever a draft invoice cannot be finalized. See the invoice’s last finalization error for details.
    case FINALIZED = 'invoice.finalized'; // Occurs whenever a draft invoice is finalized and updated to be an open invoice.
    case MARKED_UNCOLLECTIBLE = 'invoice.marked_uncollectible'; // Occurs whenever an invoice is marked uncollectible.
    case OVERDUE = 'invoice.overdue'; // Occurs X number of days after an invoice becomes due—where X is determined by Automations
    case PAID = 'invoice.paid'; // Occurs whenever an invoice payment attempt succeeds or an invoice is marked as paid out-of-band.
    case PAYMENT_ACTION_REQUIRED = 'invoice.payment_action_required'; // Occurs whenever an invoice payment attempt requires further user action to complete.
    case PAYMENT_FAILED = 'invoice.payment_failed'; // Occurs whenever an invoice payment attempt fails, due either to a declined payment or to the lack of a stored payment method.
    case PAYMENT_SUCCEEDED = 'invoice.payment_succeeded'; // Occurs whenever an invoice payment attempt succeeds.
    case SENT = 'invoice.sent'; // Occurs whenever an invoice email is sent out.
    case UPCOMING = 'invoice.upcoming'; // Occurs X number of days before a subscription is scheduled to create an invoice that is automatically charged—where X is determined by your subscriptions settings. Note: The received Invoice object will not have an invoice ID.
    case UPDATED = 'invoice.updated'; // Occurs whenever an invoice changes (e.g., the invoice amount).
    case VOIDED = 'invoice.voided'; // Occurs whenever an invoice is voided.
    case WILL_BE_DUE = 'invoice.will_be_due'; // Occurs X number of days before an invoice becomes due—where X is determined by Automations
}
