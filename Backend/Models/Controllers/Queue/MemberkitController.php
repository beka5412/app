<?php

namespace Backend\Controllers\Queue;

use Backend\App;
use Backend\Entities\Abstracts\Memberkit\MemberkitQueue;
use Backend\Http\Response;
use Backend\Models\MemberkitQueue as ModelsMemberkitQueue;
use Backend\Types\Memberkit\EMemberkitQueueStatus;

class MemberkitController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
    }

    public function handle()
    {
        $memberkit_queue = ModelsMemberkitQueue::where('status', EMemberkitQueueStatus::WAITING)->where('scheduled_at', '<=', today())->first();

        if (empty($memberkit_queue)) return Response::json([
            'status' => 'error', 
            'message' => 'Nothing to run now.',
            'data' => [
                'now' => today()
            ]
        ]);

        $memberkit_queue->status = EMemberkitQueueStatus::EXECUTED;
        $memberkit_queue->save();

        MemberkitQueue::run($memberkit_queue);
    }
}
