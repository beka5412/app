<?php

namespace Backend\Types\Memberkit;

use Backend\Models\MemberkitQueue;

class MemberkitType
{
    public ?string $data;

    public ?MemberkitQueue $entity;

    public function __construct(MemberkitQueue|array $object)
    {
        if ($object instanceof MemberkitQueue)
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
