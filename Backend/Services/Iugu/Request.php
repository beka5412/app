<?php
declare(strict_types=1);

namespace Backend\Services\Iugu;
use Backend\Http\Client\HasRequest;
use Backend\Http\Client\IRequestable;

class Request implements IRequestable
{
    use HasRequest;
    public const ENDPOINT = 'https://api.iugu.com/v1';
}
