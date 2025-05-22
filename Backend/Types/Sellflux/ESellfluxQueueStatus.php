<?php 

namespace Backend\Types\Sellflux;

enum ESellfluxQueueStatus: string
{
    case WAITING = 'waiting';
    case EXECUTED = 'executed';
    case SENT = 'sent';
    case ERROR = 'error';
}
