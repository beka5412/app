<?php

namespace Backend\Controllers\Queue;

use Backend\App;
use Backend\Entities\Abstracts\Utmify\UtmifyQueue;
use Backend\Http\Response;
use Backend\Models\UtmifyQueue as ModelsUtmifyQueue;
use Backend\Types\Utmify\EUtmifyQueueStatus;

class UtmifyAPIController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
    }

    public function handle()
    {
        $utmify_queue = ModelsUtmifyQueue::where('status', EUtmifyQueueStatus::WAITING)->where('scheduled_at', '<=', today())->first();

        if (empty($utmify_queue)) return Response::json(['status' => 'error', 'message' => 'Nothing to run now.']);

        $utmify_queue->status = EUtmifyQueueStatus::EXECUTED;
        $utmify_queue->save();

        UtmifyQueue::run($utmify_queue);
    }
}
