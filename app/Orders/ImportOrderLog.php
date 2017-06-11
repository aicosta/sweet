<?php

namespace Sweet\Orders;

use Illuminate\Database\Eloquent\Model;

class ImportOrderLog extends Model
{
    protected $fillable = ['code','message','origin'];
}
