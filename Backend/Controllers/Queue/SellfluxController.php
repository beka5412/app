<?php
declare(strict_types=1);

namespace Backend\Controllers\Queue;

use Backend\Controllers\Controller\TController;
use Backend\Entities\Abstracts\Sellflux\SellfluxQueue;
use Backend\Http\Response;
use Backend\Models\SellfluxQueue as ModelsSellfluxQueue;
use Backend\Types\Sellflux\ESellfluxQueueStatus;

class SellfluxController
{
    use TController;

    public function handle()
    {
        $queue = ModelsSellfluxQueue::where('status', ESellfluxQueueStatus::WAITING)->where('scheduled_at', '<=', today())->first();

        if (empty($queue)) return Response::json([
            'status' => 'error', 
            'message' => 'Nothing to run now.',
            'data' => [
                'now' => today()
            ]
        ]);

        $queue->status = ESellfluxQueueStatus::EXECUTED;
        $queue->save();

        SellfluxQueue::run($queue);
    }
}
