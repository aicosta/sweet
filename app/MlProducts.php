<?php

namespace Sweet;

use Illuminate\Database\Eloquent\Model;

class MlProducts extends Model
{
    protected $fillable = ['sku','mlb','url'];

    public function item(){
        return $this->belongsTo(\Sweet\Product\Products::class,'sku','sku');
    }
}
