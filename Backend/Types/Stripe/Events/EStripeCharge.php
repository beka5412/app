<?php

namespace Backend\Types\Stripe\Events;

enum EStripeCharge: string
{
    case CAPTURED = 'charge.captured'; // Occurs whenever a previously uncaptured charge is captured.
    case DISPUTE_CLOSE = 'charge.dispute.closed'; // Occurs when a dispute is closed and the dispute status changes to lost, warning_closed, or won.
    case DISPUTE_CREATED = 'charge.dispute.created'; // Occurs whenever a customer disputes a charge with their bank.
    case DISPUTE_FUNDS_REINSTATED = 'charge.dispute.funds_reinstated'; // Occurs when funds are reinstated to your account after a dispute is closed. This includes partially refunded payments.
    case DISPUTE_FUNDS_WITHDRAWN = 'charge.dispute.funds_withdrawn'; // Occurs when funds are removed from your account due to a dispute.
    case DISPUTE_UPDATED = 'charge.dispute.updated'; // Occurs when the dispute is updated (usually with evidence).
    case EXPIRED = 'charge.expired'; // Occurs whenever an uncaptured charge expires.
    case FAILED = 'charge.failed'; // Occurs whenever a failed charge attempt occurs.
    case PENDING = 'charge.pending'; // Occurs whenever a pending charge is created.
    case REFUND_UPDATED = 'charge.refund.updated'; // Occurs whenever a refund is updated, on selected payment methods.
    case REFUNDED = 'charge.refunded'; // Occurs whenever a charge is refunded, including partial refunds.
    case SUCCEEDED = 'charge.succeeded'; // Occurs whenever a charge is successful.
    case UPDATED = 'charge.updated'; // Occurs whenever a charge description or metadata is updated, or upon an asynchronous capture.
}