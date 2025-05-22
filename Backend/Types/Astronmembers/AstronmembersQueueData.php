<?php
declare(strict_types=1);

namespace Backend\Types\Astronmembers;

class AstronmembersQueueData
{
    public ?object $headers;
    public ?object $query_string;
    public ?object $payload;
    public string $uri;
    public string $verb;

    public function __construct(array $data)
    {
        $this->headers = (object) ($data['headers'] ?? null);
        $this->query_string = (object) ($data['query_string'] ?? null);
        $this->payload = (object) ($data['payload'] ?? null);
        $this->uri = $data['uri'] ?? null;
        $this->verb = $data['verb'] ?? null;
    }
}
