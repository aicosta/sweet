<?php

namespace Sweet;

use Illuminate\Database\Eloquent\Model;

class BatchLog extends Model
{
     protected $fillable = [
        'type', 'payload',
    ];
}
