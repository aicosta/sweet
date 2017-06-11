<?php

namespace Sweet\Orders;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected $fillable = ['printed','old_id','code','total','freight',
                           'comission','origin','max_date','order_statuses_id',
                           'comments_old','customers_id','envio','estimated_date'];
      private $rules = array(
        'code' => 'required|unique:orders|min:1|max:50',
        'total' => 'required',
        'freight' => 'required',
        'comission' => 'nullable',
        'origin' => 'required',
        //'max_date' = > '',
        'order_statuses_id' => 'required',
        //'comments_old' = > '',
        'customers_id' => 'required',
        //'envio' = > ''
    );
    public function items(){
    	return $this->hasMany(OrderItem::class,'orders_id');
    }
    public function customer(){
    	return $this->belongsTo(\Sweet\Customer::class,'customers_id');
    }
    public function invoice(){
    	return $this->hasOne(\Sweet\invoice::class,'orders_id');
    }
    public function shipping(){
    	return $this->hasOne(\Sweet\Shipping::class,'orders_id');
    }
    public function status(){
        return $this->belongsTo(OrderStatus::class,'order_statuses_id');
    }
}
