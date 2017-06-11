<?php

namespace Sweet\Http\Controllers;

use Illuminate\Http\Request;

use Sweet\Http\Requests;
use Sweet\Order;
use Sweet\Customer;
use Sweet\invoice;
use Sweet\Product\Providers;

class TagsController extends Controller
{
    public function getList($fornecedor = false){
        $providers = Providers::orderBy('name')->get();
        if($fornecedor){
          $orders = \DB::table('orders')
                        ->leftJoin('order_items', 'orders.id', '=', 'order_items.orders_id')
                        ->leftJoin('products', 'order_items.sku', '=', 'products.sku')
                        ->leftJoin('providers', 'providers.id', '=', 'products.providers_id')
                        ->leftJoin('invoices', 'orders.id', '=', 'invoices.orders_id')
                        ->where('order_statuses_id',2)
                        ->where('envio','<>','me2')
                        ->where('providers_id', $fornecedor)
                        ->select('orders.*', 'products.*', 'providers.name as fornecedor', 'order_items.sku as psku', 'order_items.name as pname','invoices.number')
                        ->get();
        }else{
          $orders = \DB::table('orders')
                        ->leftJoin('order_items', 'orders.id', '=', 'order_items.orders_id')
                        ->leftJoin('products', 'order_items.sku', '=', 'products.sku')
                        ->leftJoin('providers', 'providers.id', '=', 'products.providers_id')
                        ->leftJoin('invoices', 'orders.id', '=', 'invoices.orders_id')
                        ->where('order_statuses_id',2)
                        ->where('envio','<>','me2')
                        ->select('orders.*', 'products.*', 'providers.name as fornecedor', 'order_items.sku as psku', 'order_items.name as pname','invoices.number')
                        ->get();
        }
        return view('tags.list')->with(compact('providers','orders'));

    }
    public function getListMe2($fornecedor = false){
          $orders = \DB::table('orders')
                        ->join('order_items', 'orders.id', '=', 'order_items.orders_id')
                        ->join('products', 'order_items.sku', '=', 'products.sku')
                        ->join('providers', 'providers.id', '=', 'products.providers_id')
                        ->join('invoices', 'orders.id', '=', 'invoices.orders_id')
                        ->where('order_statuses_id',2)
                        ->where('envio','me2')
                        ->select('orders.*', 'products.*', 'providers.name as fornecedor','invoices.number')
                        ->get();
        return view('tags.me2')->with(compact('orders'));

    }
}
