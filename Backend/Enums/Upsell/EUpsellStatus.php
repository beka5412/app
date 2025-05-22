<?php

namespace Backend\Enums\Upsell;

enum EUpsellStatus : string
{
    case PUBLISHED = 'published';
    case DISABLED = 'disabled';
    case DRAFT = 'draft';
}