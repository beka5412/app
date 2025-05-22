<?php

namespace Backend\Types\SellerCredit;

class SellerQueueUpdateWhere
{
    public SellerCreditBodyWhere $data;
    public ESellerCreditQueueStatus $status;

    public function __construct(object|array $object)
    {
        if (gettype($object) === 'array') $object = (object) $object;

        if ($object->data ?? false) $this->data = $object->data;
        if ($object->status ?? false) $this->status = $object->status;
    }
}
