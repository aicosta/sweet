<?php

namespace Sweet\Product;

use Illuminate\Database\Eloquent\Model;

class StockLog extends Model
{
    protected $fillable = ['product_id','user_id','qty','origin'];

    public function user(){
    	return $this->belongsTo(\Sweet\User::class);
    }
}
