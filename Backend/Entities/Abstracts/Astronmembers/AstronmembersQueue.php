<?php

namespace Backend\Entities\Abstracts\Astronmembers;

use Backend\Exceptions\App\Astronmembers\AstronmembersRequestErrorException;
use Backend\Http\Response;
use Backend\Models\AstronmembersQueue as ModelsAstronmembersQueue;
use Backend\Services\Astronmembers\AstronmembersRest;
use Backend\Types\Astronmembers\AstronmembersQueueData;
use Backend\Types\Astronmembers\AstronmembersType;
use Backend\Types\Astronmembers\EAstronmembersQueueStatus;
use Backend\Types\Response\EResponseDataStatus;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;
use Exception;

class AstronmembersQueue
{
    public static function push(AstronmembersQueueData $data, string $date = ''): ModelsAstronmembersQueue
    {
        $queue = new ModelsAstronmembersQueue;
        $queue->data = json_encode($data);
        $queue->status = EAstronmembersQueueStatus::WAITING;
        $queue->scheduled_at = $date ? $date : today();
        $queue->save();
        return $queue;
    }
    
    public static function run(ModelsAstronmembersQueue $queue)
    {
        self::send([
            "data" => $queue->data,
            "entity" => $queue
        ]);
    }

    public static function send(AstronmembersType|array $queue_object): Response
    {
        $body = json_decode($queue_object instanceof AstronmembersType ? $queue_object->data : $queue_object['data']);
        $entity = $queue_object instanceof AstronmembersType ? $queue_object->entity : $queue_object['entity'];
        $headers = (array) $body->headers ?? [];
        $payload = $body->payload ?? null;
        $query_string = !empty($body->query_string ?? null) ? "?".http_build_query($body->query_string ?? []) : '';
        $verb = $body->verb ?? null;
        $uri = $body->uri ?? null;

        try
        {
            $response = AstronmembersRest::request(
                verb: $verb,
                url: $uri.$query_string,
                headers: $headers,
                body: http_build_query($payload),
                timeout: 3
            );

            if ($response->status_code !== 200) 
                throw new AstronmembersRequestErrorException;
            
            else
            {
                if ($entity instanceof ModelsAstronmembersQueue)
                {
                    $entity->status = EAstronmembersQueueStatus::SENT;
                    $entity->save();
                }
            }

            $response_data = new ResponseData(['status' => 'success', 'message' => 'Astronmembers request sent.']);
            $response_code = new ResponseStatus('200 OK');
        }

        catch (Exception|AstronmembersRequestErrorException)
        {
            $response_data = new ResponseData(['status' => 'error', 'message' => 'Astronmembers request error.']);
            $response_code = new ResponseStatus('400 Bad Request');
        }

        return self::response($entity, $response_data, $response_code);
    }

    public static function response(?ModelsAstronmembersQueue $queue, ResponseData $response_data, ResponseStatus $response_code): Response
    {
        if (($queue ?? null) instanceof ModelsAstronmembersQueue)
        {
            $queue->response = json_encode([
                "status" => $response_code->status,
                "message" => $response_data->message ?? '',
                "data" => $response_data->data ?? ''
            ]);
            if ($response_data->status === EResponseDataStatus::ERROR) $queue->status = EAstronmembersQueueStatus::ERROR;
            $queue->save();
        }

        return Response::json($response_data, $response_code);
    }
}