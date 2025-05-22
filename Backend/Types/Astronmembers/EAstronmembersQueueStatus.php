<?php 

namespace Backend\Types\Astronmembers;

enum EAstronmembersQueueStatus: string
{
    case WAITING = 'waiting';
    case EXECUTED = 'executed';
    case SENT = 'sent';
    case ERROR = 'error';
}
