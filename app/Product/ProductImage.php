<?php

namespace Sweet\Product;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = ['url','products_id'];
    protected $hidden = [
        'id','products_id','created_at', 'updated_at',
    ];

}
