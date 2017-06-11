<?php

namespace Sweet\Orders;

use Illuminate\Database\Eloquent\Model;

class OrderComment extends Model
{
    public function user(){
        return $this->belongsTo(\Sweet\User::class,'user_id');
    }
}
