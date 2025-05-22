<?php

namespace Backend\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    public $fillable = [
        'user_id',
        'checkout_id',
        'name'
    ];
}
