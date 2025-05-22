<?php 
declare(strict_types=1);

namespace Backend\Http\Client;

interface IRequestable
{
    public function request(string $verb, string $url, array $headers = [], string $body = '', int $timeout=0): object;
}