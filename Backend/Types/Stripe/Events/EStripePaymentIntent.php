<?php

namespace Backend\Types\Stripe\Events;

enum EStripePaymentIntent: string
{
    case AMOUNT_CAPTURABLE_UPDATED = 'payment_intent.amount_capturable_updated'; // Occurs when a PaymentIntent has funds to be captured. Check the amount_capturable property on the PaymentIntent to determine the amount that can be captured. You may capture the PaymentIntent with an amount_to_capture value up to the specified amount. Learn more about capturing PaymentIntents.
    case CANCELED = 'payment_intent.canceled'; // Occurs when a PaymentIntent is canceled.
    case CREATED = 'payment_intent.created'; // Occurs when a new PaymentIntent is created.
    case PARTIALLY_FUNDED = 'payment_intent.partially_funded'; // Occurs when funds are applied to a customer_balance PaymentIntent and the ‘amount_remaining’ changes.
    case PAYMENT_FAILED = 'payment_intent.payment_failed'; // Occurs when a PaymentIntent has failed the attempt to create a payment method or a payment.
    case PROCESSING = 'payment_intent.processing'; // Occurs when a PaymentIntent has started processing.
    case REQUIRES_ACTION = 'payment_intent.requires_action'; // Occurs when a PaymentIntent transitions to requires_action state
    case SUCCEEDED = 'payment_intent.succeeded'; // Occurs when a PaymentIntent has successfully completed payment.
}
