<?php

namespace Sweet\Http\Controllers\marketplaces;

use Illuminate\Http\Request;
use Sweet\Http\Requests;
use Sweet\Http\Controllers\Controller;

use Sweet\Http\Controllers\StockController;
use Sweet\Http\Controllers\PriceController;
use Sweet\Http\Controllers\OrdersController;
use Sweet\Orders\Order;
use Sweet\Shipping;
use Sweet\invoice;
use Sweet\Product\Products;
use Sweet\B2wReport;

class B2wController extends Controller
{

    private $url = 'https://api-marketplace.bonmarketplace.com.br';
    private $key = '7226B8421B13346F3E91C752943F6C4F';
    private $curl;
    private $stocks;
    private $prices;
    private $orderController;


    public function __construct(){
        $this->curl = new \anlutro\cURL\cURL;
        $this->stocks = new StockController;
        $this->prices = new PriceController;
        $this->orderController = new OrdersController;
    }
    public function orders($status = 'APPROVED'){
        $limit = 50;
        $url = $this->url.'/order?status='.$status.'&limit='.$limit.'&offset=0';
        $request = $this->curl->newRequest('get', $url)
                        ->setUser($this->key)->setPass('')
                        ->send();
        $request = json_decode($request);

        //return false;
        $pages = floor($request->total/$limit);

        for($n=0; $n<= $pages; $n++){
            if($n ==0){
                $orders = $request->orders;
            }else{
                if($n == 10){
                    break;
                }
                $url = $this->url.'/order?status='.$status.'&limit='.$limit.'&offset='.(int)$limit*$n;
                $request = $this->curl->newRequest('get', $url)
                                ->setUser($this->key)->setPass('')
                                ->send();
                $request = json_decode($request);
                $orders = $request->orders;
            }


            foreach($orders as $order){
                if($status != "APPROVED"){
                    $ret[] = $order;

                    continue;
                }

                try{

                    $type = (isset($order->customer->pf->cpf))?'PF':'PJ';

                    if(isset($order->customer->pf->cpf)){
                        $document = $order->customer->pf->cpf;
                        $name = $order->customer->pf->name;
                    }else{
                        $document = $order->customer->pj->cnpj;
                        $name = $order->customer->pj->corporateName;
                    }


                    $customer['name'] = $name;
                    $customer['document'] = $document;
                    $customer['document2'] = '';
                    $customer['type'] = $type;
                    $customer['email'] = 'hello@fullhub.com.br';
                    $customer['phone'] = $order->customer->telephones->main->ddd.' '.$order->customer->telephones->main->number;
                    $customer['phone2'] = '';

                    $customer['zip_code'] = $order->customer->deliveryAddress->zipcode;
                    $customer['address'] = $order->customer->deliveryAddress->street;
                    $customer['number'] = $order->customer->deliveryAddress->number;
                    $customer['complement'] = (isset($order->customer->deliveryAddress->additionalInfo))?$order->customer->deliveryAddress->additionalInfo:'';
                    $customer['quarter'] = $order->customer->deliveryAddress->neighborhood;
                    $customer['reference'] = (isset($order->customer->deliveryAddress->reference))?$order->customer->deliveryAddress->reference:'';
                    $customer['city'] = $order->customer->deliveryAddress->city;
                    $customer['state'] = $order->customer->deliveryAddress->state;

                    $ordernew['code'] = $order->id;
                    $ordernew['total'] = ($order->totalAmount- $order->totalFreight);
                    $ordernew['freight'] = $order->totalFreight;
                    $ordernew['comission'] = 0;
                    $ordernew['origin'] = 'B2W';
                    $ordernew['max_date'] = date('Y-m-d$ H:i:s', strtotime($order->estimatedDeliveryDate));
                    $c=0;
                    unset($item);
                    foreach($order->products as $product){
                        $item[$c]['sku'] = $product->link->id;
                        $item[$c]['name'] = ' ';
                        $item[$c]['price'] = $product->price;
                        $item[$c]['quantity'] = $product->quantity;
                        $c++;
                    }
                    $this->orderController->create($customer, $ordernew, $item);
                    if($status == 'APPROVED'){
                        $this->setStatus('PROCESSING', $order->id);
                    }




                }catch(Exception $e){

                    dd($order);
                }
            }


        }
        if($status != 'APPROVED'){
            if($pages == 1){
                return $ret;
            }
            return $ret;
        }

    }

    public function setStatus($status, $orderId, $info = false){
        $url = $this->url.'/order/'.$orderId.'/status';
        switch($status){
            case 'PROCESSING';
                $data['status']= 'PROCESSING';
                break;
            case 'INVOICED';
                $data = $info;
                break;
            case 'SHIPPED';
                $data = $info;
                break;
            case 'DELIVERED';
                $data = $info;
                break;
        }

        $request = $this->curl->newJsonRequest('put', $url, $data)
                        ->setUser($this->key)->setPass('')
                        ->setHeader('Content-type', 'application/json;charset=UTF-8')
                        ->send();

        if($request->statusCode == '204'){
            echo $orderId.' - '.$status.'<br />';
        }else{
            $request = json_decode($request->body);

            echo $orderId.' - '.$request->message.'<br />';
        }
        /*{
          "shipmentException": {
            "occurrenceDate": "string",
            "observation": "string"
          },
          "shipped": {
            "trackingProtocol": "string",
            "estimatedDelivery": "string",
            "deliveredCarrierDate": "string",
            "carrierName": "string",
            "trackingUrl": "string"
          },
          "unavailable": {
            "observation": "string",
            "unavailableDate": "string"
          },
          "delivered": {
            "deliveredCustomerDate": "string"
          },
          "invoiced": {
            "number": "integer",
            "line": "integer",
            "issueDate": "string",
            "key": "string",
            "danfeXml": "string"
          },
          "status": "string"
        }*/
    }

    public function fixOrders(){
        $orders = Order::where('origin', 'B2W')
                        ->where('order_statuses_id', 9)
                        /*->orWhere('order_statuses_id',2)
                        ->orWhere('order_statuses_id',3)
                        ->orWhere('order_statuses_id',4)
                        ->orWhere('order_statuses_id',5)
                        ->orWhere('order_statuses_id',9)*/
                        ->get();

        foreach($orders as $order){
            $url = $this->url.'/order/'.$order->code;
            $request = $this->curl->newRequest('get', $url)
                                ->setUser($this->key)->setPass('')
                                ->send();
            $request = json_decode($request);

            if($request->status == 'DELIVERED' || $request->status == 'REVIEW'){
                $inv = true;
                $order->order_statuses_id = 6;
            }else if($request->status == 'SHIPPED'){
                $inv = true;
                $order->order_statuses_id = 2;
            }else if($request->status == 'CANCELED'){
                $inv = false;
                $order->order_statuses_id = 8;
            }else if($request->status != 'PROCESSING'){

                dd($request);
            }
            $order->save();
            if($inv){
                $invoice = invoice::where('orders_id',$order->id)->get();
                if(count($invoice) == 0){
                    unset($invoice);
                    $invoice = new invoice();
                    $invoice->orders_id = $order->id;
                    $invoice->number = $request->invoiced->number;
                    $invoice->serie = $request->invoiced->line;
                    $invoice->key = (isset($request->invoiced->key))?$request->invoiced->key:'';
                    $invoice->save();
                }
            }

        }
    }

    public function productFix(){
        $orders = Order::with('items')->where('order_statuses_id',1)->where('origin','B2W')
                        ->get();
        foreach($orders as $order){
            foreach($order->items as $item){

                $it = \Sweet\Product\Products::where('sku',$item->sku)->get();
                if(count($it) == 0){
                    $url = $this->url.'/sku/'.$item->sku;
                    $request = $this->curl->newRequest('get', $url)
                                    ->setUser($this->key)->setPass('')
                                    ->send();
                    $sku = json_decode($request);
                    unset($product);
                    $product = new \Sweet\Product\Products();
                    $product->sku = $sku->id;
                    $product->name = $sku->name;
                    $product->description = $sku->description;
                    $product->short_description = '';
                    $product->providers_id = 65;
                    $product->brand = ' ';
                    $product->lead_time = 3;
                    $product->status = 1;

                    $product->save();
                    $fiscals = new \Sweet\Product\ProductFiscal();
                    $fiscals->products_id = $product->id;
                    $fiscals->sku = $sku->id;
                    $fiscals->name = $sku->name;
                    $fiscals->ean = (isset($sku->ean[0]))?$sku->ean[0]:' ';
                    $fiscals->ncm = '';
                    $fiscals->isbn = '';
                    $fiscals->origin = 1;
                    $fiscals->save();

                    $dimensions =  new \Sweet\Product\ProductDimension();
                    $dimensions->products_id = $product->id;
                    $dimensions->weight = $sku->weight;
                    $dimensions->width = $sku->width;
                    $dimensions->height = $sku->height;
                    $dimensions->depth = $sku->length;
                    $dimensions->cube = 0;
                    $dimensions->save();

                    $stock = new \Sweet\Product\ProductExternalStock();
                    $stock->products_id = $product->id;
                    $stock->quantity = $sku->stockQuantity;
                    $stock->save();

                    $price = new \Sweet\Product\ProductPrices();
                    $price->products_id = $product->id;
                    $price->marketplaces_id = 1;
                    $price->price = $sku->price->sellPrice;
                    $price->save();
                    $c=0;
                    foreach($sku->urlImage as $img){
                        $image[$c] = new \Sweet\Product\ProductImage();
                        $image[$c]->products_id =  $product->id;
                        $image[$c]->url = $img;
                        $image[$c]->save();
                        $c++;
                    }
                    echo $sku->id." Cadastrado!\n";
                }else{
                    echo "Produto já cadastrado<br />";
                }
            }


        }
    }
    public function fdfas(){
        $limit = 50;
        $url = $this->url.'/sku?limit='.$limit.'&offset=0';
        $request = $this->curl->newRequest('get', $url)
                        ->setUser($this->key)->setPass('')
                        ->send();
        $request = json_decode($request);
        $pages = floor($request->total/$limit);
        ob_start();
        for($n=$pages; $n<= $pages; $n--){
            if($n ==0){

                flush();
                ob_flush();
                $items = $request->skus;
            }else{
                $url = $this->url.'/sku?limit='.$limit.'&offset='.(int)$limit*$n;
                $request = $this->curl->newRequest('get', $url)
                                ->setUser($this->key)->setPass('')
                                ->send();
                $request = json_decode($request);
                $items = $request->skus;
            }
            echo "\nLendo página ".($n+1)."\n===================\n\n";
            foreach($items as $sku){
                $item = \Sweet\Product\Products::where('sku',$sku->id)->get();

                if(count($item) == 0){
                    unset($product);
                    $product = new \Sweet\Product\Products();
                    $product->sku = $sku->id;
                    $product->name = $sku->name;
                    $product->description = $sku->description;
                    $product->short_description = '';
                    $product->providers_id = 65;
                    $product->brand = ' ';
                    $product->lead_time = 3;
                    $product->status = 1;

                    $product->save();
                    $fiscals = new \Sweet\Product\ProductFiscal();
                    $fiscals->products_id = $product->id;
                    $fiscals->sku = $sku->id;
                    $fiscals->name = $sku->name;
                    $fiscals->ean = (isset($sku->ean[0]))?$sku->ean[0]:' ';
                    $fiscals->ncm = '';
                    $fiscals->isbn = '';
                    $fiscals->origin = 1;
                    $fiscals->save();

                    $dimensions =  new \Sweet\Product\ProductDimension();
                    $dimensions->products_id = $product->id;
                    $dimensions->weight = $sku->weight;
                    $dimensions->width = $sku->width;
                    $dimensions->height = $sku->height;
                    $dimensions->depth = $sku->length;
                    $dimensions->cube = 0;
                    $dimensions->save();

                    $stock = new \Sweet\Product\ProductExternalStock();
                    $stock->products_id = $product->id;
                    $stock->quantity = $sku->stockQuantity;
                    $stock->save();

                    $price = new \Sweet\Product\ProductPrices();
                    $price->products_id = $product->id;
                    $price->marketplaces_id = 1;
                    $price->price = $sku->price->sellPrice;
                    $price->save();
                    $c=0;
                    foreach($sku->urlImage as $img){
                        $image[$c] = new \Sweet\Product\ProductImage();
                        $image[$c]->products_id =  $product->id;
                        $image[$c]->url = $img;
                        $image[$c]->save();
                        $c++;
                    }
                    echo $sku->id." Cadastrado!\n";
                }else{
                    echo $sku->id." Encontrado!\n";
                }
                flush();
                ob_flush();

            }
        }
    }



    public function invoice(){
        $orders = $this->orders('PROCESSING');

        foreach($orders as $o){
            $ship = Order::with('invoice')->where('code',$o->id)->first();
            if(isset($ship->invoice) && $ship->invoice->key != NULL){
                unset($data);
                $data['status'] = 'INVOICED';
                $data['invoiced']['number'] = $ship->invoice->number;
                $data['invoiced']['line'] = $ship->invoice->serie;
                $data['invoiced']['issueDate'] = date('Y-m-d H:i:s');
                $data['invoiced']['key'] = $ship->invoice->key;
                $this->setStatus('INVOICED', $ship->code, $data);
            }else{

            }

        }
    }
    public function shipping(){
        $orders = $this->orders('INVOICED');

        foreach($orders as $o){
            $ship = Order::with('shipping')->where('code',$o->id)->first();


            if(isset($ship->shipping) && $ship->shipping->shipping_code != NULL){
                $data['status'] = 'SHIPPED';
                $data['shipped']['trackingProtocol'] = $ship->shipping->shipping_code;
                $data['shipped']['estimatedDelivery'] = date('Y-m-d H:i:s', strtotime('+5 days'));
                $data['shipped']['deliveredCarrierDate'] = date('Y-m-d H:i:s');
                $this->setStatus('SHIPPED', $ship->code, $data);
            }else{
                echo $o->id.' - Nenhum Rastreio Encontrado<br />';
            }

        }
    }
    public function finish(){
        $orders = $this->orders('SHIPPED');

        foreach($orders as $o){
            $ship = Order::with('shipping')->where('code',$o->id)->where('order_statuses_id','6')->first();

            if(isset($ship->shipping) && $ship->shipping->shipping_code != NULL){

                $data['status'] = 'DELIVERED';
                $data['delivered']['deliveredCustomerDate'] = date('Y-m-d H:i:s');

                $this->setStatus('DELIVERED', $ship->code, $data);
            }else{
                echo $o->id.' Pedido ainda não Finalizado<br />';
            }

        }
    }




    public function updateStocks(){
        $list = $this->stocks->getUpdated();
        $n=0;
        $c =0;

        if(!$list){
            return ['Message' => 'Nenhum Produto para atualizar'];
        }
        foreach($list as $l){
            if($n == 500){ $c++; }
            $data[$c]['skus'][$n] = ['id' => $l['sku'], 'stockQuantity' => $l['total']];
            $n++;
        }
        return $data;
    }



    public function stockReport($id){
        
        $url = $this->url.'/batch/sku/report/'.$id;
        $request = $this->curl->newRequest('get', $url)
                        ->setUser($this->key)->setPass('')
                        ->send();
        $data = json_decode($request->body);
        dd($data);
        dd($request);
    }
    public function price(){
        echo "\n\n\n ## Preços B2W ## \n\n\n";
        $items = $this->prices->getUpdated();
        if(!$items){
            return 'Nenhum Produto para Atualizar';
        }
        $url = $this->url.'/batch/sku/price';
        $n=0;
        $c =0;
        foreach($items as $i){
            $por = number_format($i['b2w_tam_cnova'],2,'.','');
            $de = number_format(($i['b2w_tam_cnova']*1.2),2,'.','');
            $data[$c]['skus'][$n] = [
                                     'id' => $i['sku'], 
                                     'price' => ['listPrice' => $de, 'sellPrice' =>$por]
                                    ];
            $n++;
            if($n == 1000){
                $n=0;
                $c++;
            }
        }
        foreach($data as $d){
            $request = $this->curl->newJsonRequest('post', $url, $d)
                            ->setHeader('Content-type', 'application/json;charset=UTF-8')
                            ->setUser($this->key)
                            ->setPass('');
            $response = $request->send();
            if($response->statusCode == 202){
                $r = json_decode($response->body);
                echo 'Carga Enviada '.$r->id."\n";
            }
            flush();
            ob_flush();
        }
    }
    public function stock(){

        echo "\n\n\n ## Estoque B2W ## \n\n\n";
        $items = $this->stocks->getUpdated();
        
         if(!$items){
            return 'Nenhum Produto para Atualizar';
         }
        $url = $this->url.'/batch/sku/stock';
        $n=0;
        $c =0;

        foreach($items as $i){
            $i['total'] = ($i['total']<0)?0:$i['total'];
            $data[$c]['skus'][$n] = ['id' => $i['sku'], 'stockQuantity'=> $i['total']];
            $n++;
            if($n == 1000){
                $n=0;
                $c++;
            }
        }
        foreach($data as $d){
            $request = $this->curl->newJsonRequest('post', $url, $d)
                            ->setHeader('Content-type', 'application/json;charset=UTF-8')
                            ->setUser($this->key)
                            ->setPass('');
            $response = $request->send();
            if($response->statusCode == 202){
                $r = json_decode($response->body);
                echo 'Carga Enviada '.$r->id."\n";
            }
            flush();
            ob_flush();
        }
    }
    public function stockAll(){
        $url = $this->url.'/batch/sku/stock';
        $products = Products::where('providers_id',64)->get();

        $n=0;
        $c =0;
        foreach($products as $product){
            $stock = $this->stocks->getStock($product->sku);
            $data[$c]['skus'][$n] = ['id' => $product->sku, 'stockQuantity'=> $stock['total']];
            $n++;
            if($n == 1000){
                $n=0;
                $c++;
            }
        }
        foreach($data as $d){
            $request = $this->curl->newJsonRequest('post', $url, $d)
                            ->setHeader('Content-type', 'application/json;charset=UTF-8')
                            ->setUser($this->key)
                            ->setPass('');
            $response = $request->send();
            if($response->statusCode == 202){
                $r = json_decode($response->body);
                echo 'Carga Enviada '.$r->id.'<br >';
            }
            flush();
            ob_flush();
        }
    }

    public function canceledOrders(){
        $status = 'CANCELED';
        $orders = $this->orders($status);
        foreach($orders as $o){
            dd($o);
        }
    }
    public function products(){
        $limit= 50;
        $url = $this->url.'/sku?limit='.$limit.'&offset=0';
        $request = $this->curl->newRequest('get', $url)
                        ->setUser($this->key)->setPass('')
                        ->send();
        $response = json_decode($request->body);
        $pages = floor($response->total/$limit);

        for($n = 1140; $n <= $pages; $n++){
            echo $n."\n";
            flush();
            ob_flush();
            if($n ==0){
                $prod = $response->skus;
            }else{
                $url = $this->url.'/sku?limit='.$limit.'&offset='.$limit*$n;
                $request = $this->curl->newRequest('get', $url)
                                ->setUser($this->key)->setPass('')
                                ->send();
                $response = json_decode($request->body);
                $prod = $response->skus;
            }
            foreach($prod as $p){
                unset($report);
                $price = (isset($p->price->listPrice))?$p->price->listPrice:0;
                $stock = (isset($p->stockQuantity))?$p->stockQuantity:0;
                $check = B2wReport::where('sku',$p->id)->get();
                if(count($check) == 0){
                    $report = ['sku' => $p->id,'price' => $price, 'stock' => $stock];
                    B2wReport::create($report);    
                }
            }

        }
    }
}
