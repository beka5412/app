<?php
declare(strict_types=1);

namespace Backend\Services\Memberkit;
use Backend\Http\Client\HasRequest;
use Backend\Http\Client\IRequestable;

class Request implements IRequestable
{
    use HasRequest;
    public const ENDPOINT = 'https://memberkit.com.br/api/v1';
}
