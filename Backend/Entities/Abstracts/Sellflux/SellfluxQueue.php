<?php
declare(strict_types=1);

namespace Backend\Entities\Abstracts\Sellflux;

use Backend\Exceptions\App\Sellflux\SellfluxRequestErrorException;
use Backend\Http\Response;
use Backend\Models\SellfluxQueue as ModelsSellfluxQueue;
use Backend\Services\Sellflux\SellfluxRest;
use Backend\Types\Sellflux\SellfluxQueueData;
use Backend\Types\Response\EResponseDataStatus;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;
use Backend\Types\Sellflux\ESellfluxQueueStatus;
use Backend\Types\Sellflux\SellfluxType;
use Exception;

class SellfluxQueue
{
    public static function push(SellfluxQueueData $data, string $date = ''): ModelsSellfluxQueue
    {
        $queue = new ModelsSellfluxQueue;
        $queue->data = json_encode($data);
        $queue->status = ESellfluxQueueStatus::WAITING;
        $queue->scheduled_at = $date ? $date : today();
        $queue->save();
        return $queue;
    }
    
    public static function run(ModelsSellfluxQueue $queue)
    {
        self::send([
            "data" => $queue->data,
            "entity" => $queue
        ]);
    }

    public static function send(SellfluxType|array $queue_object): Response
    {
        $body = json_decode($queue_object instanceof SellfluxType ? $queue_object->data : $queue_object['data']);
        $entity = $queue_object instanceof SellfluxType ? $queue_object->entity : $queue_object['entity'];
        $headers = (array) $body->headers ?? [];
        $payload = $body->payload ?? null;
        $query_string = !empty($body->query_string ?? null) ? "?".http_build_query($body->query_string ?? []) : '';
        $verb = $body->verb ?? null;
        $uri = $body->uri ?? null;

        try
        {
            $response = SellfluxRest::request(
                verb: $verb,
                url: $uri.$query_string,
                headers: $headers,
                body: http_build_query($payload),
                timeout: 3
            );

            if ($response->status_code !== 200) 
                throw new SellfluxRequestErrorException;
            
            else
            {
                if ($entity instanceof ModelsSellfluxQueue)
                {
                    $entity->status = ESellfluxQueueStatus::SENT;
                    $entity->save();
                }
            }

            $response_data = new ResponseData(['status' => 'success', 'message' => 'Sellflux request sent.']);
            $response_code = new ResponseStatus('200 OK');
        }

        catch (Exception|SellfluxRequestErrorException)
        {
            $response_data = new ResponseData(['status' => 'error', 'message' => 'Sellflux request error.']);
            $response_code = new ResponseStatus('400 Bad Request');
        }

        return self::response($entity, $response_data, $response_code);
    }

    public static function response(?ModelsSellfluxQueue $queue, ResponseData $response_data, ResponseStatus $response_code): Response
    {
        if (($queue ?? null) instanceof ModelsSellfluxQueue)
        {
            $queue->response = json_encode([
                "status" => $response_code->status,
                "message" => $response_data->message ?? '',
                "data" => $response_data->data ?? ''
            ]);
            if ($response_data->status === EResponseDataStatus::ERROR) $queue->status = ESellfluxQueueStatus::ERROR;
            $queue->save();
        }

        return Response::json($response_data, $response_code);
    }
}
