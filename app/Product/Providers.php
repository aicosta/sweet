<?php

namespace Sweet\Product;

use Illuminate\Database\Eloquent\Model;

class Providers extends Model
{
    public function Products(){
    	return $this->hasMany(\Sweet\Product\Products::class);
    }
}
