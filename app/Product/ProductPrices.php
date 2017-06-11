<?php

namespace Sweet\product;

use Illuminate\Database\Eloquent\Model;

class ProductPrices extends Model
{
    protected $fillable = ['marketplaces_id','price','products_id'];
    protected $hidden = [
        'id','created_at', 'updated_at',
    ];
}
