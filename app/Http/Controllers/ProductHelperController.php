<?php

namespace Sweet\Http\Controllers;

use Illuminate\Http\Request;
use Sweet\Product\Providers;
use Sweet\Http\Requests;
use Sweet\Product\Products;

class ProductHelperController extends Controller
{

    public static function getProductBySku($sku){
        return Products::where('sku',$sku)->first() ?? false;

    }
	public static function getProvider($name){
		$provider = Providers::where('name',$name)->first();
		return $provider;
	}

	public static function checkSku($sku){
		$product = Products::where('sku',$sku)->first();

		return ($product== NULL)?false:$product;
	}

}
