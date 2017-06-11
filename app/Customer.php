<?php

namespace Sweet;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name','document','document2','type','email',
                           'phone','phone2','zip_code','address','number',
                           'complement','quarter','reference','city','state'];
    private $rules = array(
        'name' => 'required',
        'document' => 'required|unique:customers|min:1|max:20',
        'document2' => 'nullable',
        'type' => 'required|min:2|max:2',
        'email' => 'required|min:5|max:255',
        'phone' => 'required|min:1|max:20',
        'phone2' => 'nullable',
        'zip_code' => 'required|min:1|max:9',
        'address' => 'required|min:2|max:255',
        'number' => 'required|min:2|max:255',
        'complement' => 'required|min:2|max:255',
        'quarter' => 'required|min:2|max:255',
        'reference' => 'required|min:2|max:255',
        'city' => 'required|min:2|max:255',
        'state' => 'required|min:2|max:2'
    );

    public function orders(){
        return $this->hasMany(\Sweet\Order::class,'customers_id');
    }
}
