<?php

namespace Backend\Controllers\Webhooks\Stripe;

use Backend\App;
use Backend\Entities\Abstracts\Stripe\StripeWebhookQueue;
use Backend\Http\Request;
use Backend\Http\Response;

class StripeController
{
	public App $application;

	public function __construct(App $application)
	{
		$this->application = $application;
	}

	public function wook(Request $request)
	{
		header('Content-Type: application/json');

		$endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

		$payload = $request->raw();

		$bypass = $request->header("Bypass");
		$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

		\Stripe\Stripe::setAppInfo(
			"stripe-samples/accept-a-payment/payment-element",
			"0.0.2",
			"https://github.com/stripe-samples"
		);

		if ($bypass <> env('STRIPE_WEBHOOK_BYPASS'))
		{
			try
			{
				$event = \Stripe\Event::constructFrom(json_decode($payload, true));
			}

			catch (\UnexpectedValueException $e)
			{
				return Response::json(['status' => 'error', 'message' => 'Webhook error while parsing basic request.'], '401 Unauthorized');
			}

			if ($endpoint_secret)
			{
				try
				{
					\Stripe\Webhook::constructEvent(
						$payload,
						$sig_header,
						$endpoint_secret
					);
				}

				catch (\Stripe\Exception\SignatureVerificationException $e)
				{
					return Response::json(['status' => 'error', 'message' => 'Webhook error while validating signature.'], '401 Unauthorized');
				}
			}
		}


		StripeWebhookQueue::push($payload, date("Y-m-d H:i:s", strtotime(today() . " + 30 seconds")), $bypass === env('STRIPE_WEBHOOK_BYPASS'));

		return Response::json(['status' => 'success', 'message' => 'Webhook added to queue.'], '200 OK');
	}
}
