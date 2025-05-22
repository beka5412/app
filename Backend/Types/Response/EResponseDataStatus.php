<?php

namespace Backend\Types\Response;

enum EResponseDataStatus: string
{
    case SUCCESS = 'success';
    case ERROR = 'error';
}