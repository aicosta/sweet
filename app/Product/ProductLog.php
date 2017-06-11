<?php

namespace Sweet\product;

use Illuminate\Database\Eloquent\Model;

class ProductLog extends Model
{
    public function product(){
    	$this->belongsTo(\App\Sweet\Product\Products::class);
    }
}
