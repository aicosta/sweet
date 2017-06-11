<?php

namespace Sweet\Product;

use Illuminate\Database\Eloquent\Model;

class ProductDimension extends Model
{
    protected $fillable = ['weight','width','height','depth','cube','products_id'];
    protected $hidden = [
        'id','products_id','created_at', 'updated_at',
    ];
}
