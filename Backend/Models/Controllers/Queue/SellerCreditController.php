<?php

namespace Backend\Controllers\Queue;

use Backend\App;
use Backend\Entities\Abstracts\SellerCreditQueue;
use Backend\Http\Response;
use Backend\Models\SellerCreditQueue as ModelsSellerCreditQueue;
use Backend\Types\Response\EResponseDataStatus;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;
use Backend\Types\SellerCredit\ESellerCreditQueueStatus;

class SellerCreditController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
    }

    public function handle()
    {
        $entities = ModelsSellerCreditQueue::where('status', ESellerCreditQueueStatus::WAITING)->where('scheduled_at', '<=', today())->get();

        if (empty($entities)) return Response::json(['status' => 'error', 'message' => 'Nothing to run now.']);

        $result = [];

        foreach ($entities as $entity)
        {
            $entity->status = ESellerCreditQueueStatus::EXECUTED;
            $entity->executed_at = today();
            $entity->save();

            $result[] = SellerCreditQueue::run($entity);
        }

        return Response::json(
            new ResponseData([
                'status' => count($result) ? EResponseDataStatus::SUCCESS : EResponseDataStatus::ERROR, 
                'message' => count($result) ? 'Users successfully credited.' : 'Nothing to run now.',
                'data' => $result
            ]), 
            new ResponseStatus('200 OK')
        );
    }
}