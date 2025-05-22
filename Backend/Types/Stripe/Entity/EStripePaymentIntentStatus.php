<?php

namespace Backend\Types\Stripe\Entity;

enum EStripePaymentIntentStatus: string
{
    case AMOUNT_CAPTURABLE_UPDATED = 'amount_capturable_updated'; // Occurs when a PaymentIntent has funds to be captured. Check the amount_capturable property on the PaymentIntent to determine the amount that can be captured. You may capture the PaymentIntent with an amount_to_capture value up to the specified amount. Learn more about capturing PaymentIntents.
    case CANCELED = 'canceled'; // Occurs when a PaymentIntent is canceled.
    case CREATED = 'created'; // Occurs when a new PaymentIntent is created.
    case PARTIALLY_FUNDED = 'partially_funded'; // Occurs when funds are applied to a customer_balance PaymentIntent and the ‘amount_remaining’ changes.
    case PAYMENT_FAILED = 'payment_failed'; // Occurs when a PaymentIntent has failed the attempt to create a payment method or a payment.
    case PROCESSING = 'processing'; // Occurs when a PaymentIntent has started processing.
    case REQUIRES_ACTION = 'requires_action'; // Occurs when a PaymentIntent transitions to requires_action state
    case SUCCEEDED = 'succeeded'; // Occurs when a PaymentIntent has successfully completed payment.
}
