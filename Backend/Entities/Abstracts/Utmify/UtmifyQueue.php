<?php

namespace Backend\Entities\Abstracts\Utmify;

use Backend\Exceptions\Utmify\UtmifyRequestErrorException;
use Backend\Http\Response;
use Backend\Models\UtmifyQueue as ModelsUtmifyQueue;
use Backend\Services\Utmify\Instance;
use Backend\Services\Utmify\UtmifyRest;
use Backend\Types\Response\EResponseDataStatus;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;
use Backend\Types\Utmify\EUtmifyQueueStatus;
use Backend\Types\Utmify\UtmifyType;
use Exception;

class UtmifyQueue
{
    public static function push(?string $data, string $date = ''): void
    {
        $utmify = new ModelsUtmifyQueue;
        $utmify->data = $data;
        $utmify->status = EUtmifyQueueStatus::WAITING;
        $utmify->scheduled_at = $date ? $date : today();
        $utmify->save();
    }
    
    public static function run(ModelsUtmifyQueue $webhook_queue)
    {
        self::send([
            "data" => $webhook_queue->data,
            "entity" => $webhook_queue
        ]);
    }

    public static function send(UtmifyType|array $utmify_queue_object)
    {
        $body = json_decode($utmify_queue_object instanceof UtmifyType ? $utmify_queue_object->data : $utmify_queue_object['data']);
        $entity = $utmify_queue_object instanceof UtmifyType ? $utmify_queue_object->entity : $utmify_queue_object['entity'];
        $headers = $body->headers ?? [];
        if (!empty($headers))
        {
            $headers->{'Content-Type'} = 'application/json';
            $headers = (array) $headers;
        }
        $payload = $body->payload ?? null;

        try
        {
            $response = UtmifyRest::request(
                verb: 'POST',
                url: '/orders',
                headers: $headers,
                body: json_encode($payload),
                timeout: 3
            );

            if ($response->status_code !== 200) 
                throw new UtmifyRequestErrorException;
            
            else
            {
                if ($entity instanceof ModelsUtmifyQueue)
                {
                    $entity->status = EUtmifyQueueStatus::SENT;
                    $entity->save();
                }
            }

            $response_data = new ResponseData(['status' => 'success', 'message' => 'UTM sent.']);
            $response_code = new ResponseStatus('200 OK');
        }

        catch (Exception|UtmifyRequestErrorException)
        {
            $response_data = new ResponseData(['status' => 'error', 'message' => 'UTM error.', 'data' => $response]);
            $response_code = new ResponseStatus('400 Bad Request');
        }

        return self::response($entity, $response_data, $response_code);
    }

    public static function response(?ModelsUtmifyQueue $utmify_queue, ResponseData $response_data, ResponseStatus $response_code): Response
    {
        if (($utmify_queue ?? null) instanceof ModelsUtmifyQueue)
        {
            $utmify_queue->response = json_encode([
                "status" => $response_code->status,
                "message" => $response_data->message ?? '',
                "data" => $response_data->data ?? ''
            ]);
            if ($response_data->status === EResponseDataStatus::ERROR) $utmify_queue->status = EUtmifyQueueStatus::ERROR;
            $utmify_queue->save();
        }

        return Response::json($response_data, $response_code);
    }
}