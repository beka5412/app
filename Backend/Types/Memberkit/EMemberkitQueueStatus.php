<?php 

namespace Backend\Types\Memberkit;

enum EMemberkitQueueStatus: string
{
    case WAITING = 'waiting';
    case EXECUTED = 'executed';
    case SENT = 'sent';
    case ERROR = 'error';
}