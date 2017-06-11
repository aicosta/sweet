<?php

namespace Sweet\Product;

use Illuminate\Database\Eloquent\Model;

class ProductFiscal extends Model
{
    protected $fillable = ['sku','name','ean','ncm','isbn','origin','products_id'];
    protected $hidden = [
        'id','products_id','created_at', 'updated_at',
    ];
}
