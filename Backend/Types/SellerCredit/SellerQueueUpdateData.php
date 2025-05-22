<?php

namespace Backend\Types\SellerCredit;

class SellerQueueUpdateData
{
    public ?string $data;
    public ?string $response;
    public ?string $scheduled_at;
    public ?int $current_attempt;
    public ?ESellerCreditQueueStatus $status;
    public ?string $executed_at;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct(object|array $object)
    {
        if (gettype($object) === 'array') $object = (object) $object;

        if ($object->data ?? false) $this->data = $object->data;
        if ($object->response ?? false) $this->response = $object->response;
        if ($object->scheduled_at ?? false) $this->scheduled_at = $object->scheduled_at;
        if ($object->current_attempt ?? false) $this->current_attempt = $object->current_attempt;
        if ($object->status ?? false) $this->status = $object->status;
        if ($object->executed_at ?? false) $this->executed_at = $object->executed_at;
        if ($object->created_at ?? false) $this->created_at = $object->created_at;
        if ($object->updated_at ?? false) $this->updated_at = $object->updated_at;
    }
}
