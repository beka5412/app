<?php

namespace Backend\Types\SellerCredit;

enum ESellerCreditQueueStatus: string
{
    case WAITING = 'waiting'; // occurs when the registration is waiting to be executed
    case EXECUTED = 'executed'; // occurs when it was executed
    case SENT = 'sent'; // occurs when the execution was successful
    case ERROR = 'error'; // occurs when there were errors in execution
    case CANCELED = 'canceled'; // occurs when execution is no longer necessary
}
