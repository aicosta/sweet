<?php

namespace Sweet\Http\Controllers;

use Illuminate\Http\Request;

use Sweet\Http\Requests;
use DB;
use Sweet\Product\Products;
use Sweet\Http\Controllers\ProductHelperController;
use Sweet\Product\ProductPrices;
use Auth;
use Regulus\ActivityLog\Models\Activity		;

class PriceController extends Controller
{
	private $time = 3; //tempo em minutos
    public function getUpdated(){
    	$date = date('Y-m-d H:i:s', strtotime('-'.$this->time.' minutes'));
    	$updateds = ProductPrices::where('updated_at','>', $date)->get();
    	$n=0;
    	if(count($updateds) == 0){
    		return false;
    	}
    	foreach($updateds as $updated){
    		$prodSku = Products::find($updated->products_id);
    		$data[$n]['sku'] = $prodSku->sku;
    		$data[$n]['b2w_tam_cnova'] = $updated->price;
    		$data[$n]['mobly_wal'] = $updated->price2;
    		$data[$n]['ml'] = $updated->price3;
    		$n++;
    	}

    	return $data;
    } 
}
