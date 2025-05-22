<?php

namespace Backend\Enums\Product;

enum EProductDelivery : string
{
    case DOWNLOAD = 'download';
    case MEMBERKIT = 'memberkit';
    case CADEMI = 'cademi';
    case ROCKETMEMBER = 'rocketmember';
    case ASTRONMEMBERS = 'astronmembers';
    case EXTERNAL = 'external';
    case NOTHING = 'nothing';
}
