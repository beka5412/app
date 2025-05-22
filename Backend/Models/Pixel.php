<?php 

namespace Backend\Models;

use Illuminate\Database\Eloquent\Model;
use Backend\Models\Domain;

class Pixel extends Model
{
    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }
}