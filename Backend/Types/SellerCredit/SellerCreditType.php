<?php

namespace Backend\Types\SellerCredit;

use Backend\Models\SellerCreditQueue;

class SellerCreditType
{
    public ?string $data;

    public ?SellerCreditQueue $entity;

    public function __construct(SellerCreditQueue|array $object)
    {
        if ($object instanceof SellerCreditQueue)
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
