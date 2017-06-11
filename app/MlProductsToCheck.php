<?php

namespace Sweet;

use Illuminate\Database\Eloquent\Model;

class MlProductsToCheck extends Model
{
	protected $table = 'ml_products_to_check';
    public function products()
    {
        return $this->hasOne(\Sweet\Product\Products::class);
    }
}
