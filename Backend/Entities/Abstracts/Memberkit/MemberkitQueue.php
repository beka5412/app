<?php

namespace Backend\Entities\Abstracts\Memberkit;

use Backend\Exceptions\App\Memberkit\MemberkitRequestErrorException;
use Backend\Http\Response;
use Backend\Models\MemberkitQueue as ModelsMemberkitQueue;
use Backend\Services\Memberkit\MemberkitRest;
use Backend\Types\Memberkit\MemberkitQueueData;
use Backend\Types\Response\EResponseDataStatus;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;
use Backend\Types\Memberkit\EMemberkitQueueStatus;
use Backend\Types\Memberkit\MemberkitType;
use Exception;

class MemberkitQueue
{
    public static function push(MemberkitQueueData $data, string $date = ''): ModelsMemberkitQueue
    {
        $queue = new ModelsMemberkitQueue;
        $queue->data = json_encode($data);
        $queue->status = EMemberkitQueueStatus::WAITING;
        $queue->scheduled_at = $date ? $date : today();
        $queue->save();
        return $queue;
    }
    
    public static function run(ModelsMemberkitQueue $queue)
    {
        self::send([
            "data" => $queue->data,
            "entity" => $queue
        ]);
    }

    public static function send(MemberkitType|array $queue_object): Response
    {
        $body = json_decode($queue_object instanceof MemberkitType ? $queue_object->data : $queue_object['data']);
        $entity = $queue_object instanceof MemberkitType ? $queue_object->entity : $queue_object['entity'];
        $headers = (array) $body->headers ?? [];
        $payload = $body->payload ?? null;
        $query_string = !empty($body->query_string ?? null) ? "?".http_build_query($body->query_string ?? []) : '';

        try
        {
            $response = MemberkitRest::request(
                verb: 'POST',
                url: "/users$query_string",
                headers: $headers,
                body: json_encode($payload),
                timeout: 3
            );

            if ($response->status_code !== 200 && $response->status_code !== 201) 
                throw new MemberkitRequestErrorException;
            
            else
            {
                if ($entity instanceof ModelsMemberkitQueue)
                {
                    $entity->status = EMemberkitQueueStatus::SENT;
                    $entity->save();
                }
            }

            $response_data = new ResponseData(['status' => 'success', 'message' => 'Memberkit request sent.']);
            $response_code = new ResponseStatus('200 OK');
        }

        catch (Exception|MemberkitRequestErrorException)
        {
            $response_data = new ResponseData(['status' => 'error', 'message' => 'Memberkit request error.']);
            $response_code = new ResponseStatus('400 Bad Request');
        }

        return self::response($entity, $response_data, $response_code);
    }

    public static function response(?ModelsMemberkitQueue $queue, ResponseData $response_data, ResponseStatus $response_code): Response
    {
        if (($queue ?? null) instanceof ModelsMemberkitQueue)
        {
            $queue->response = json_encode([
                "status" => $response_code->status,
                "message" => $response_data->message ?? '',
                "data" => $response_data->data ?? ''
            ]);
            if ($response_data->status === EResponseDataStatus::ERROR) $queue->status = EMemberkitQueueStatus::ERROR;
            $queue->save();
        }

        return Response::json($response_data, $response_code);
    }
}