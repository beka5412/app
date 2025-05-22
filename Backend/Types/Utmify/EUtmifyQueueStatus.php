<?php 

namespace Backend\Types\Utmify;

enum EUtmifyQueueStatus: string
{
    case WAITING = 'waiting';
    case EXECUTED = 'executed';
    case SENT = 'sent';
    case ERROR = 'error';
}
