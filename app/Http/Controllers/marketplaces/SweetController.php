<?php

namespace Sweet\Http\Controllers\marketplaces;

use Illuminate\Http\Request;

use Sweet\Http\Requests;
use Sweet\Http\Controllers\Controller;
use Sweet\Product\Products;
use Sweet\Product\ProductAttribute;
use Sweet\Product\ProductDimension;
use Sweet\Product\ProductExternalStock;
use Sweet\Product\ProductFiscal;
use Sweet\Product\ProductImage;
use Sweet\Product\ProductPrices;
use Sweet\Product\ProductOrigin;
use Sweet\Product\ProductLog;
use Sweet\Product\ProductCategory;
use Sweet\Product\StockLog;

class SweetController extends Controller
{
    private $user  = 'a@a.com';
    private $pass = 'd3hd3hd3h';
    private $url = 'https://api.fullhub.com.br/';
    private $curl;
    private $token;

    public function __construct(){
        $this->curl = new \anlutro\cURL\cURL;
        $this->generateToken();

    }
    private function generateToken(){
        $url = $this->url.'auth?email='.$this->user.'&password='.$this->pass;
        $response = $this->curl->newRequest('get', $url)
                                ->send();
        $response = json_decode($response->body);

        $this->token = $response->token;
    }
    public function dev(){
        $url = $this->url.'/getUpdated?token='.$this->token;
        $response = $this->curl->newRequest('get', $url)
                                ->send();
        $data = json_decode($response->body);
        foreach($data as $p){
            $prod = Products::with('stocks')->where('sku', $p->sku)->first();
            if(count($prod) > 0){
                if($prod->stocks->quantity != $p->qty){
                     $prod->stocks->quantity =$p->qty;
                    if($prod->stocks->save()){
                        unset($log);
                        $log = ['product_id' => $prod->id, 'user_id' => 17, 'qty' => $p->qty, 'origin' => 'Planilha BookPartners'];
                        StockLog::create($log);
                        echo $p->sku." Atualizado\n";
                    }
                }else{
                    echo $p->sku." Já Atualizado\n";
                }
                
            }else{
                echo $p->sku." Não Localizado\n";
            }
                flush();
                ob_flush();
        }
    }
    public function dev2(){
        $limit = 1000;
        $url = $this->url.'/products?token='.$this->token;
        $response = $this->curl->newRequest('get', $url)
                                ->send();
        $data = json_decode($response->body);

        for($n=1; $n<= $data[0]->last_page; $n++){
            if($n == 1){
                $items = $data[0]->data;
            }else{
                 $url = $this->url.'/products?page='.$n.'&token='.$this->token;
                 $response = $this->curl->newRequest('get', $url)
                                ->send();
                $data = json_decode($response->body);
                //dd($data);
                $items = $data[0]->data;
            }

            foreach($items as $item){
                $prod = Products::with('stocks')->where('sku', $item->sku)->first();

                if(count($prod) == 0){
                    if($item->images[0]->internal_url == ''){
                        continue;
                    }

                    unset($product);
                    unset($attrs);
                    unset($dimensions);
                    unset($stock);
                    unset($fiscals);
                    unset($image);
                    unset($price);
                    $product['sku'] = $item->sku;
                    $product['name'] = $item->name;
                    $product['description'] = $item->description;
                    $product['providers_id'] = 64;
                    $product['brand'] = $item->brand;
                    $product['lead_time'] = 3;
                    $product['status'] = 1;
                    $product['nameml'] = substr($item->name,0,60);




                    $product = Products::create($product);
                    //
                    foreach($item->specifications as $specifications){
                        $attrs[] = ['name'=> $specifications->name,'value' => $specifications->value];
                    }

                    foreach($attrs as $attr){
                        $attr['products_id'] = $product->id;
                        ProductAttribute::create($attr);
                    }
                    $dimensions['weight'] = $item->weight;
                    $dimensions['width'] = $item->width;
                    $dimensions['height'] = $item->height;
                    $dimensions['depth'] = $item->length;
                    $dimensions['cube'] = 0;
                    $dimensions['products_id'] = $product->id;
                    ProductDimension::create($attr);

                    $stock['quantity'] = $item->qty;
                    $stock['products_id'] = $product->id;
                    ProductExternalStock::create($stock);

                    $fiscals['sku'] = $item->sku;
                    $fiscals['name'] = $item->name;
                    $fiscals['ean'] = $item->ean;
                    $fiscals['ncm'] = $item->ncm;
                    $fiscals['isbn'] = $item->isbn;
                    $fiscals['origin'] = 1;
                    $fiscals['products_id'] = $product->id;
                    ProductFiscal::create($fiscals);

                    $image['url'] = 'https://api.fullhub.com.br'.$item->images[0]->internal_url;
                    $images['products_id'] = $product->id;
                    ProductImage::create($images);

                    $price['marketplaces_id'] = 1;
                    $price['price'] = $item->price;
                    $price['products_id'] = $product->id;
                    ProductPrices::create($price);

                    echo $product->sku.' Cadastrado <br />';
                }
                flush();
                ob_flush();
            }
        }
    }
    public function dev3(){
        $limit = 1000;
        $url = $this->url.'/products?token='.$this->token;
        $response = $this->curl->newRequest('get', $url)
                                ->send();
        $data = json_decode($response->body);

        for($n=1; $n<= $data[0]->last_page; $n++){
            if($n == 1){
                $items = $data[0]->data;
            }else{
                 $url = $this->url.'/products?page='.$n.'&token='.$this->token;
                 $response = $this->curl->newRequest('get', $url)
                                ->send();
                $data = json_decode($response->body);
                //dd($data);
                $items = $data[0]->data;
            }

            foreach($items as $item){
                $prod = Products::with('stocks')->where('sku', $item->sku)->first();

                if(count($prod) > 0){
                    $prod->stocks->quantity =$item->qty;
                    $prod->stocks->save();
                    echo $item->sku.' Atualizado<Br />';
                }else{
                    echo $item->sku.' Não Localizado<Br />';
                }
                flush();
                ob_flush();
            }
        }
    }
}
