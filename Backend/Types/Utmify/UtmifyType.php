<?php

namespace Backend\Types\Utmify;

use Backend\Models\UtmifyQueue;

class UtmifyType
{
    public ?string $data;

    public ?UtmifyQueue $entity;

    public function __construct(UtmifyQueue|array $object)
    {
        if ($object instanceof UtmifyQueue)
        {
            $this->data = $object->data;
            $this->entity = $object;
        }
        else
        {
            $this->data = $object["data"];
            $this->entity = $object["entity"];
        }
    }
}
