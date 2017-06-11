<?php

namespace Sweet\Product;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $fillable = ['sku', 'name','description','providers_id','brand','lead_time','status','nameml'];
    public function images()
    {
		return $this->hasMany(\Sweet\Product\ProductImage::class);
    }

    public function stocks()
    {
    	return $this->hasOne(\Sweet\Product\ProductExternalStock::class,'products_id');
    }
    public function providers(){
    	return $this->belongsTo(\Sweet\Product\Providers::class);
    }
    public function dimensions(){
        return $this->hasOne(\Sweet\Product\ProductDimension::class);
    }
    public function fiscals(){
        return $this->hasOne(\Sweet\Product\ProductFiscal::class,'products_id');
    }

    public function prices()
    {
        return $this->hasOne(\Sweet\Product\ProductPrices::class);
    }

    public function categories()
    {
        return $this->hasOne(\Sweet\Product\ProductCategory::class);
    }
}
