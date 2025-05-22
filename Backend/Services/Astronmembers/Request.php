<?php
declare(strict_types=1);

namespace Backend\Services\Astronmembers;
use Backend\Http\Client\HasRequest;
use Backend\Http\Client\IRequestable;

class Request implements IRequestable
{
    use HasRequest;
    public const ENDPOINT = 'https://api.astronmembers.com.br/v1.0';
}
