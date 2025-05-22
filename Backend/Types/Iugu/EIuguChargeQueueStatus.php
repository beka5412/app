<?php 

namespace Backend\Types\Iugu;

enum EIuguChargeQueueStatus: string
{
    case WAITING = 'waiting';
    case EXECUTED = 'executed';
    case SENT = 'sent';
    case ERROR = 'error';
}
