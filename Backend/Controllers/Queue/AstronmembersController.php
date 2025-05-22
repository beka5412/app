<?php
declare(strict_types=1);

namespace Backend\Controllers\Queue;

use Backend\Controllers\Controller\TController;
use Backend\Entities\Abstracts\Astronmembers\AstronmembersQueue;
use Backend\Http\Response;
use Backend\Models\AstronmembersQueue as ModelsAstronmembersQueue;
use Backend\Types\Astronmembers\EAstronmembersQueueStatus;

class AstronmembersController
{
    use TController;

    public function handle()
    {
        $queue = ModelsAstronmembersQueue::where('status', EAstronmembersQueueStatus::WAITING)->where('scheduled_at', '<=', today())->first();

        if (empty($queue)) return Response::json([
            'status' => 'error', 
            'message' => 'Nothing to run now.',
            'data' => [
                'now' => today()
            ]
        ]);

        $queue->status = EAstronmembersQueueStatus::EXECUTED;
        $queue->save();

        AstronmembersQueue::run($queue);
    }
}
