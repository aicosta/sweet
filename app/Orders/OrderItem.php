<?php

namespace Sweet\Orders;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['sku','name','price','quantity','orders_id'];
    private $rules = array(
        'sku' => 'required|min:1|max:30',
        'name' => 'required|min:1|max:150',
        'price' => 'required',
        'quantity' => 'required',
        'orders_id' => 'required',
    );
	public function product(){
		return $this->belongsTo(\Sweet\Product\Products::class,'sku','sku');
	}
}
