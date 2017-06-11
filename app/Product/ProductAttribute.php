<?php

namespace Sweet\Product;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    protected $fillable = ['name','value','products_id'];
}
