<?php
declare(strict_types=1);

namespace Backend\Types\Sellflux;

use Backend\Models\SellfluxQueue;

class SellfluxType
{
    public ?string $data;

    public ?SellfluxQueue $entity;

    public function __construct(SellfluxQueue|array $object)
    {
        if ($object instanceof SellfluxQueue)
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
