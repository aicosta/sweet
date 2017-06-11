<?php

namespace Sweet\Orders;

use Illuminate\Database\Eloquent\Model;

class OrderLogs extends Model
{
    protected $fillable = ['order_id','user_id','old_status','new_status'];



    public function user(){
        return $this->belongsTo(\Sweet\User::class);
    }
}
