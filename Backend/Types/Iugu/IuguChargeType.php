<?php
declare(strict_types=1);

namespace Backend\Types\Iugu;

use Backend\Models\IuguChargeQueue;

class IuguChargeType
{
    public ?string $data;

    public ?IuguChargeQueue $entity;

    public function __construct(IuguChargeQueue|array $object)
    {
        if ($object instanceof IuguChargeQueue)
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
