<?php 

namespace Backend\Enums\Orderbump;

enum EOrderbumpStatus : String
{
    case PUBLISHED = 'published';
    case DISABLED = 'disabled';
    case DRAFT = 'draft';
}