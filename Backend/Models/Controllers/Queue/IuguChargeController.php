<?php

namespace Backend\Controllers\Queue;

use Backend\App;
use Backend\Controllers\Controller\TController;
use Backend\Entities\Abstracts\Iugu\IuguChargeQueue;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Models\IuguChargeQueue as ModelsIuguChargeQueue;
use Backend\Types\Iugu\EIuguChargeQueueStatus;

class IuguChargeController
{
    use TController;

    public function handle(Request $request)
    {
        $id = $request->query('id');
        if ($id)
        {
            $queue = ModelsIuguChargeQueue::where('id', $id)->first();

            if (empty($queue)) return Response::json(['status' => 'error', 'message' => 'Item not found.']);
        }
        else
        {
            $queue = ModelsIuguChargeQueue::where('status', EIuguChargeQueueStatus::WAITING)->where('scheduled_at', '<=', today())->first();

            if (empty($queue)) return Response::json(['status' => 'error', 'message' => 'Nothing to run now.']);
        }

        $queue->status = EIuguChargeQueueStatus::EXECUTED;
        $queue->save();

        IuguChargeQueue::run($queue);
    }
}
