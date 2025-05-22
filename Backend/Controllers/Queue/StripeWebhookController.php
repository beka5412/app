<?php

namespace Backend\Controllers\Queue;

use Backend\App;
use Backend\Entities\Abstracts\Stripe\StripeWebhookQueue;
use Backend\Http\Response;
use Backend\Models\WebhookQueue;

class StripeWebhookController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
    }

    public function handle()
    {
        $webhook_queue = WebhookQueue::where('status', 'waiting')->where('scheduled_at', '<=', today())->first();

        if (empty($webhook_queue)) 
            return Response::json(
                [
                    'status' => 'error', 
                    'message' => 'Nothing to run now.', 
                    'data' => [
                        'now' => today()
                    ]
                ]
            );

        $webhook_queue->status = 'executed';
        $webhook_queue->save();

        StripeWebhookQueue::run($webhook_queue);
    }
}
