<?php

namespace Backend\Types\Memberkit;

class MemberkitQueueData
{
    public ?object $headers;
    public ?object $query_string;
    public ?object $payload;

    public function __construct(array $data)
    {
        $this->headers = (object) ($data['headers'] ?? null);
        $this->query_string = (object) ($data['query_string'] ?? null);
        $this->payload = (object) ($data['payload'] ?? null);
    }
}
