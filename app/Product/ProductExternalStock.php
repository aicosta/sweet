<?php

namespace Sweet\Product;

use Illuminate\Database\Eloquent\Model;

class ProductExternalStock extends Model
{
    protected $fillable = ['quantity','products_id'];
    protected $hidden = [
        'id','products_id','created_at', 'updated_at',
    ];
}
