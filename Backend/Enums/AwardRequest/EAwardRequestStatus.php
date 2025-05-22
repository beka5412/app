<?php

namespace Backend\Enums\AwardRequest;

enum EAwardRequestStatus: string
{
    case PENDING = 'pending';
    case SENT = 'sent';
    case CANCELED = 'canceled';
}
