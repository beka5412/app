<?php
declare(strict_types=1);

namespace Backend\Types\Astronmembers;

use Backend\Models\AstronmembersQueue;

class AstronmembersType
{
    public ?string $data;

    public ?AstronmembersQueue $entity;

    public function __construct(AstronmembersQueue|array $object)
    {
        if ($object instanceof AstronmembersQueue)
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
