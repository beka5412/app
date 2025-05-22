<?php 

namespace Backend\Enums\Product;

enum ERecurrenceInterval : string
{
    case DAY = 'day';
    case WEEK = 'week';
    case MONTH = 'month';
    case YEAR = 'year';
}