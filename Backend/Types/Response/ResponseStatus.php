<?php

namespace Backend\Types\Response;

class ResponseStatus
{
    public string $status;

    public function __construct(string $status)
    {
        $this->status = $status;
    }
}