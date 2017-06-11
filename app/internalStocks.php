<?php

namespace Sweet;

use Illuminate\Database\Eloquent\Model;

class internalStocks extends Model
{
    protected $fillable = ['product_id','ins','outs','observation','origin','balance'];
    public function product(){
        return $this->belongsTo(\Sweet\Product\Products::class);
    }
}
